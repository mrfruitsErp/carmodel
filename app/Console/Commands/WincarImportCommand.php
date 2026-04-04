<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\{Customer, Vehicle, WorkOrder, Tenant, WincarImportLog};
use Illuminate\Support\Facades\DB;
use League\Csv\Reader;

class WincarImportCommand extends Command
{
    protected $signature = 'wincar:import 
                            {file : Percorso del file CSV esportato da Wincar}
                            {--tenant= : ID del tenant (obbligatorio)}
                            {--type=customers : Tipo di import: customers, vehicles, jobs}
                            {--dry-run : Simula senza salvare}';

    protected $description = 'Importa dati da export CSV di Wincar';

    public function handle(): int
    {
        $file = $this->argument('file');
        $tenantId = $this->option('tenant');
        $type = $this->option('type');
        $dryRun = $this->option('dry-run');

        if (!$tenantId) {
            $this->error('Specificare --tenant=ID');
            return 1;
        }

        if (!file_exists($file)) {
            $this->error("File non trovato: {$file}");
            return 1;
        }

        $tenant = Tenant::findOrFail($tenantId);
        $this->info("Importazione per: {$tenant->name}");
        $this->info("Tipo: {$type} | Dry-run: " . ($dryRun ? 'SI' : 'NO'));

        $log = WincarImportLog::create([
            'tenant_id' => $tenantId,
            'file_name' => basename($file),
            'import_type' => $type,
            'status' => 'running',
            'imported_by' => null,
        ]);

        try {
            $csv = Reader::createFromPath($file, 'r');
            $csv->setHeaderOffset(0);
            $records = iterator_to_array($csv->getRecords());
            $log->update(['rows_total' => count($records)]);

            $imported = $skipped = $errors = 0;
            $bar = $this->output->createProgressBar(count($records));

            DB::beginTransaction();

            foreach ($records as $row) {
                try {
                    $result = match($type) {
                        'customers' => $this->importCustomer($row, $tenantId, $dryRun),
                        'vehicles'  => $this->importVehicle($row, $tenantId, $dryRun),
                        'jobs'      => $this->importJob($row, $tenantId, $dryRun),
                        default     => throw new \Exception("Tipo non supportato: {$type}"),
                    };
                    $result ? $imported++ : $skipped++;
                } catch (\Exception $e) {
                    $errors++;
                    $this->warn("\nErrore riga: " . $e->getMessage());
                }
                $bar->advance();
            }

            if ($dryRun) {
                DB::rollBack();
                $this->info("\n[DRY RUN] Nessuna modifica salvata.");
            } else {
                DB::commit();
            }

            $bar->finish();
            $log->update([
                'rows_imported' => $imported,
                'rows_skipped' => $skipped,
                'rows_error' => $errors,
                'status' => 'completed',
                'completed_at' => now(),
            ]);

            $this->newLine();
            $this->table(['Totale','Importati','Saltati','Errori'], [
                [count($records), $imported, $skipped, $errors]
            ]);
            return 0;

        } catch (\Exception $e) {
            DB::rollBack();
            $log->update(['status' => 'failed', 'error_log' => $e->getMessage()]);
            $this->error("Errore critico: " . $e->getMessage());
            return 1;
        }
    }

    private function importCustomer(array $row, int $tenantId, bool $dry): bool
    {
        // Mappa campi Wincar → CarModel (adatta alle colonne reali del tuo Wincar)
        $data = [
            'tenant_id'    => $tenantId,
            'type'         => !empty($row['PIVA']) ? 'company' : 'private',
            'first_name'   => $row['NOME'] ?? null,
            'last_name'    => $row['COGNOME'] ?? null,
            'company_name' => $row['RAGIONE_SOCIALE'] ?? null,
            'fiscal_code'  => $row['CODICE_FISCALE'] ?? null,
            'vat_number'   => $row['PIVA'] ?? null,
            'email'        => $row['EMAIL'] ?? null,
            'phone'        => $row['TELEFONO'] ?? null,
            'address'      => $row['INDIRIZZO'] ?? null,
            'city'         => $row['CITTA'] ?? null,
            'postal_code'  => $row['CAP'] ?? null,
            'province'     => $row['PROVINCIA'] ?? null,
            'source'       => 'wincar_import',
        ];

        if ($dry) return true;

        // Cerca duplicati per CF o P.IVA
        $existing = Customer::where('tenant_id', $tenantId)
            ->when($data['fiscal_code'], fn($q) => $q->orWhere('fiscal_code', $data['fiscal_code']))
            ->when($data['vat_number'], fn($q) => $q->orWhere('vat_number', $data['vat_number']))
            ->first();

        if ($existing) {
            // Aggiorna solo campi vuoti
            $existing->fill(array_filter($data, fn($v) => $v !== null))->save();
            return false; // skipped (aggiornato)
        }

        Customer::create($data);
        return true;
    }

    private function importVehicle(array $row, int $tenantId, bool $dry): bool
    {
        $plate = strtoupper(trim($row['TARGA'] ?? ''));
        if (!$plate) return false;

        $customer = null;
        if (!empty($row['CLIENTE_CF'])) {
            $customer = Customer::where('tenant_id', $tenantId)
                ->where('fiscal_code', $row['CLIENTE_CF'])->first();
        }

        $data = [
            'tenant_id'   => $tenantId,
            'customer_id' => $customer?->id ?? 1, // fallback, gestire manualmente
            'plate'       => $plate,
            'vin'         => $row['TELAIO'] ?? null,
            'brand'       => $row['MARCA'] ?? null,
            'model'       => $row['MODELLO'] ?? null,
            'year'        => $row['ANNO'] ?? null,
            'fuel_type'   => $this->mapFuelType($row['ALIMENTAZIONE'] ?? ''),
            'km_current'  => intval($row['KM'] ?? 0),
        ];

        if ($dry) return true;

        Vehicle::firstOrCreate(
            ['tenant_id' => $tenantId, 'plate' => $plate],
            $data
        );
        return true;
    }

    private function importJob(array $row, int $tenantId, bool $dry): bool
    {
        // Implementazione specifica per lavorazioni Wincar
        // I campi variano molto - da verificare con export reale
        if ($dry) return true;
        return false; // da completare con campi reali Wincar
    }

    private function mapFuelType(string $wincarValue): string
    {
        return match(strtolower(trim($wincarValue))) {
            'benzina', 'b' => 'benzina',
            'diesel', 'gasolio', 'd' => 'diesel',
            'gpl', 'g' => 'gpl',
            'metano', 'm' => 'metano',
            'elettrico', 'e', 'bev' => 'elettrico',
            'ibrido', 'hybrid', 'h', 'hev', 'phev' => 'ibrido',
            default => 'altro',
        };
    }
}
