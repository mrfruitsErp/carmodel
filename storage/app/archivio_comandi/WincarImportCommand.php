<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Customer;
use App\Models\Vehicle;
use App\Models\InsuranceCompany;
use App\Models\Expert;
use App\Models\Tenant;

class WincarImport extends Command
{
    protected $signature = 'wincar:import
                            {file : Percorso del file MDB di Wincar (wcTabelle.mdb)}
                            {--dry-run : Mostra cosa verrebbe importato senza salvare}
                            {--only= : Importa solo: clienti, veicoli, assicurazioni, periti, legali}
                            {--tenant= : ID tenant (default: 1)}';

    protected $description = 'Importa dati da Wincar (wcTabelle.mdb) in CarModel ERP';

    private bool  $dryRun   = false;
    private int   $tenantId = 1;
    private array $stats    = [];

    public function handle(): int
    {
        $file = $this->argument('file');

        if (!file_exists($file)) {
            $this->error("File non trovato: {$file}");
            return 1;
        }

        exec('which mdb-export 2>/dev/null', $out, $code);
        if ($code !== 0) {
            $this->error('mdbtools non installato.');
            $this->line('  Ubuntu/Debian: apt install mdbtools');
            $this->line('  Windows: usare WSL o copiare il file su server Linux');
            return 1;
        }

        $this->dryRun   = (bool)$this->option('dry-run');
        $this->tenantId = (int)($this->option('tenant') ?? Tenant::first()?->id ?? 1);
        $only           = $this->option('only');

        if ($this->dryRun) {
            $this->warn('⚠️  DRY-RUN attivo: nessun dato verrà salvato');
        }

        $this->info("📂 File   : {$file}");
        $this->info("🏢 Tenant : {$this->tenantId}");
        $this->newLine();

        $tasks = [
            'assicurazioni' => fn() => $this->importAssicurazioni($file),
            'periti'        => fn() => $this->importPeriti($file),
            'legali'        => fn() => $this->importLegali($file),
            'clienti'       => fn() => $this->importClienti($file),
            'veicoli'       => fn() => $this->importVeicoli($file),
        ];

        foreach ($tasks as $name => $fn) {
            if ($only && $only !== $name) continue;
            $fn();
            $this->newLine();
        }

        // Riepilogo finale
        $this->info('══════════════════════════════════════════════════');
        $this->info('  📊 RIEPILOGO IMPORTAZIONE WINCAR → CARMODEL');
        $this->info('══════════════════════════════════════════════════');
        foreach ($this->stats as $label => $s) {
            $this->line(sprintf(
                '  %-22s  ✅ %4d  ⏭️  %4d  ❌ %4d',
                $label, $s['imported'], $s['skipped'], $s['errors']
            ));
        }
        $this->info('══════════════════════════════════════════════════');

        if ($this->dryRun) {
            $this->warn('⚠️  DRY-RUN: nessun dato salvato. Rimuovi --dry-run per importare.');
        } else {
            $this->info('✅ Importazione completata!');
        }

        return 0;
    }

    // ────────────────────────────────────────────────────────────────────────
    // ASSICURAZIONI
    // Fillable: tenant_id, name, code, email, phone, fax, address, portal_url, notes, active
    // ────────────────────────────────────────────────────────────────────────
    private function importAssicurazioni(string $file): void
    {
        $this->info('🏦 Importo compagnie assicurative...');
        $rows  = $this->readMdb($file, 'ASSICU');
        $stats = ['imported' => 0, 'skipped' => 0, 'errors' => 0];
        $bar   = $this->output->createProgressBar(count($rows));
        $bar->start();

        foreach ($rows as $row) {
            $name = $this->val($row, 'F_DESASS');
            if (!$name) { $stats['skipped']++; $bar->advance(); continue; }

            if (InsuranceCompany::where('tenant_id', $this->tenantId)->where('name', $name)->exists()) {
                $stats['skipped']++; $bar->advance(); continue;
            }

            if (!$this->dryRun) {
                try {
                    InsuranceCompany::create([
                        'tenant_id' => $this->tenantId,
                        'name'      => $name,
                        'code'      => $this->val($row, 'F_CODANIA') ?: null,
                        'phone'     => $this->val($row, 'F_NUMTEL') ?: null,
                        'fax'       => $this->val($row, 'F_NUMFAX') ?: null,
                        'email'     => $this->val($row, 'F__EMAIL') ?: null,
                        'address'   => trim(
                            implode(', ', array_filter([
                                $this->val($row, 'F_INDASS'),
                                $this->val($row, 'F_CAPASS'),
                                $this->val($row, 'F_CITTAS'),
                                $this->val($row, 'F_PROASS'),
                            ]))
                        ) ?: null,
                        'notes'     => $this->memo($row, 'F_NOTEAS'),
                        'active'    => true,
                    ]);
                    $stats['imported']++;
                } catch (\Exception $e) {
                    $this->warn(" ⚠️  {$name}: " . $e->getMessage());
                    $stats['errors']++;
                }
            } else {
                $stats['imported']++;
            }
            $bar->advance();
        }

        $bar->finish();
        $this->stats['Assicurazioni'] = $stats;
        $this->line("  ✅ {$stats['imported']} importate | ⏭️  {$stats['skipped']} saltate | ❌ {$stats['errors']} errori");
    }

    // ────────────────────────────────────────────────────────────────────────
    // PERITI
    // Fillable: tenant_id, type, name, title, company_name, insurance_company_id,
    //           email, phone, phone2, address, fiscal_code, vat_number, rating, notes, active
    // ────────────────────────────────────────────────────────────────────────
    private function importPeriti(string $file): void
    {
        $this->info('🔍 Importo periti...');
        $rows  = $this->readMdb($file, 'PERITI');
        $stats = ['imported' => 0, 'skipped' => 0, 'errors' => 0];
        $bar   = $this->output->createProgressBar(count($rows));
        $bar->start();

        foreach ($rows as $row) {
            $name = $this->val($row, 'F_NOMPER');
            if (!$name) { $stats['skipped']++; $bar->advance(); continue; }

            if (Expert::where('tenant_id', $this->tenantId)->where('name', $name)->where('type', 'perito')->exists()) {
                $stats['skipped']++; $bar->advance(); continue;
            }

            if (!$this->dryRun) {
                try {
                    Expert::create([
                        'tenant_id' => $this->tenantId,
                        'type'      => 'perito',
                        'name'      => $name,
                        'phone'     => $this->val($row, 'F_TELPER') ?: null,
                        'phone2'    => $this->val($row, 'F_CELPER') ?: null,
                        'email'     => $this->val($row, 'F__EMAIL') ?: null,
                        'address'   => trim(
                            implode(', ', array_filter([
                                $this->val($row, 'F_VIAPER'),
                                $this->val($row, 'F_CAPPER'),
                                $this->val($row, 'F_CITPER'),
                                $this->val($row, 'F_PROPER'),
                            ]))
                        ) ?: null,
                        'notes'     => $this->memo($row, 'F_NOTPER'),
                        'active'    => true,
                    ]);
                    $stats['imported']++;
                } catch (\Exception $e) {
                    $this->warn(" ⚠️  {$name}: " . $e->getMessage());
                    $stats['errors']++;
                }
            } else {
                $stats['imported']++;
            }
            $bar->advance();
        }

        $bar->finish();
        $this->stats['Periti'] = $stats;
        $this->line("  ✅ {$stats['imported']} importati | ⏭️  {$stats['skipped']} saltati | ❌ {$stats['errors']} errori");
    }

    // ────────────────────────────────────────────────────────────────────────
    // LEGALI
    // ────────────────────────────────────────────────────────────────────────
    private function importLegali(string $file): void
    {
        $this->info('⚖️  Importo avvocati/legali...');
        $rows  = $this->readMdb($file, 'LEGALI');
        $stats = ['imported' => 0, 'skipped' => 0, 'errors' => 0];
        $bar   = $this->output->createProgressBar(count($rows));
        $bar->start();

        foreach ($rows as $row) {
            $name = $this->val($row, 'F_LEG_RAGSOC');
            if (!$name) { $stats['skipped']++; $bar->advance(); continue; }

            if (Expert::where('tenant_id', $this->tenantId)->where('name', $name)->where('type', 'legale')->exists()) {
                $stats['skipped']++; $bar->advance(); continue;
            }

            if (!$this->dryRun) {
                try {
                    Expert::create([
                        'tenant_id'  => $this->tenantId,
                        'type'       => 'legale',
                        'name'       => $name,
                        'phone'      => $this->val($row, 'F_LEG_TELEFO') ?: null,
                        'phone2'     => $this->val($row, 'F_LEG_CELLUL') ?: null,
                        'email'      => $this->val($row, 'F_LEG__EMAIL') ?: null,
                        'vat_number' => $this->val($row, 'F_LEG___PIVA') ?: null,
                        'fiscal_code'=> $this->val($row, 'F_LEG_CODFIS') ?: null,
                        'address'    => trim(
                            implode(', ', array_filter([
                                $this->val($row, 'F_LEG_INDIRI'),
                                $this->val($row, 'F_LEG____CAP'),
                                $this->val($row, 'F_LEG__CITTA'),
                                $this->val($row, 'F_LEG_PROVIN'),
                            ]))
                        ) ?: null,
                        'notes'      => $this->memo($row, 'F_LEG___NOTE'),
                        'active'     => true,
                    ]);
                    $stats['imported']++;
                } catch (\Exception $e) {
                    $this->warn(" ⚠️  {$name}: " . $e->getMessage());
                    $stats['errors']++;
                }
            } else {
                $stats['imported']++;
            }
            $bar->advance();
        }

        $bar->finish();
        $this->stats['Legali/Avvocati'] = $stats;
        $this->line("  ✅ {$stats['imported']} importati | ⏭️  {$stats['skipped']} saltati | ❌ {$stats['errors']} errori");
    }

    // ────────────────────────────────────────────────────────────────────────
    // CLIENTI
    // Fillable: tenant_id, type, first_name, last_name, fiscal_code, date_of_birth,
    //           company_name, vat_number, sdi_code, pec_email, email, phone, phone2,
    //           whatsapp, address, city, postal_code, province, country, notes,
    //           tags, source, total_value, active, created_by
    // ────────────────────────────────────────────────────────────────────────
    private function importClienti(string $file): void
    {
        $this->info('👥 Importo clienti...');
        $rows  = $this->readMdb($file, 'CLIENT');
        $stats = ['imported' => 0, 'skipped' => 0, 'errors' => 0];
        $bar   = $this->output->createProgressBar(count($rows));
        $bar->start();

        foreach ($rows as $row) {
            $firstName = $this->val($row, 'F___NOME');
            $lastName  = $this->val($row, 'F_COGNOM');
            $ragSoc    = $this->val($row, 'F_RAGSOC');
            $isPrivato = (int)($row['F_PRIVATO'] ?? 0) === -1; // -1 = privato in Wincar

            // Determina nome
            if ($firstName && $lastName) {
                $type = 'privato';
            } elseif ($ragSoc) {
                // Potrebbe essere azienda o privato senza split nome/cognome
                $parts     = preg_split('/\s+/', trim($ragSoc), 2);
                $lastName  = $parts[0] ?? $ragSoc;
                $firstName = $parts[1] ?? '';
                $type      = $isPrivato ? 'privato' : 'azienda';
            } else {
                $stats['skipped']++; $bar->advance(); continue;
            }

            $cf    = $this->val($row, 'F_CODFIS') ?: null;
            $email = $this->val($row, 'F__EMAIL') ?: null;
            $piva  = $this->val($row, 'F_PARIVA') ?: null;

            // Deduplicazione per CF o email
            if ($cf || $email) {
                $exists = Customer::where('tenant_id', $this->tenantId)
                    ->where(function ($q) use ($cf, $email) {
                        if ($cf)    $q->orWhere('fiscal_code', $cf);
                        if ($email) $q->orWhere('email', $email);
                    })->exists();
                if ($exists) { $stats['skipped']++; $bar->advance(); continue; }
            }

            if (!$this->dryRun) {
                try {
                    Customer::create([
                        'tenant_id'    => $this->tenantId,
                        'type'         => $type,
                        'first_name'   => $firstName,
                        'last_name'    => $lastName,
                        'company_name' => !$isPrivato ? $ragSoc : null,
                        'fiscal_code'  => $cf,
                        'vat_number'   => $piva,
                        'email'        => $email,
                        'pec_email'    => $this->val($row, 'F_EMAPEC') ?: null,
                        'phone'        => $this->val($row, 'F_TELEFO') ?: null,
                        'phone2'       => $this->val($row, 'F_TELEF2') ?: null,
                        'whatsapp'     => $this->val($row, 'F_CELLUL') ?: null,
                        'address'      => $this->val($row, 'F_VIACLI') ?: null,
                        'city'         => $this->val($row, 'F_CITTAC') ?: null,
                        'postal_code'  => $this->val($row, 'F_CAPCLI') ?: null,
                        'province'     => $this->val($row, 'F_PROCLI') ?: null,
                        'country'      => 'IT',
                        'date_of_birth'=> $this->parseDate($row['F_DATNAS'] ?? ''),
                        'notes'        => $this->memo($row, 'F_NOTCLI'),
                        'source'       => 'wincar',
                        'active'       => true,
                    ]);
                    $stats['imported']++;
                } catch (\Exception $e) {
                    $this->warn(" ⚠️  {$lastName} {$firstName}: " . $e->getMessage());
                    $stats['errors']++;
                }
            } else {
                $stats['imported']++;
            }
            $bar->advance();
        }

        $bar->finish();
        $this->stats['Clienti'] = $stats;
        $this->line("  ✅ {$stats['imported']} importati | ⏭️  {$stats['skipped']} saltati | ❌ {$stats['errors']} errori");
    }

    // ────────────────────────────────────────────────────────────────────────
    // VEICOLI
    // Fillable: tenant_id, customer_id, plate, vin, brand, model, version, year,
    //           color, fuel_type, km_current, insurance_company, insurance_policy,
    //           insurance_expiry, revision_expiry, status, notes
    // ────────────────────────────────────────────────────────────────────────
    private function importVeicoli(string $file): void
    {
        $this->info('🚗 Importo veicoli...');
        $rows  = $this->readMdb($file, 'Veicoli');
        $stats = ['imported' => 0, 'skipped' => 0, 'errors' => 0];
        $bar   = $this->output->createProgressBar(count($rows));
        $bar->start();

        // Mappa codice cliente Wincar → customer_id CarModel
        // Leggiamo tutti i clienti Wincar per fare il join sul codice
        $clientRows = $this->readMdb($file, 'CLIENT');
        $clientMap  = []; // F_CODCLI => customer record
        foreach ($clientRows as $c) {
            $cod = $c['F_CODCLI'] ?? null;
            if ($cod) $clientMap[(int)$cod] = $c;
        }

        foreach ($rows as $row) {
            $plate = $this->val($row, 'F_VEI_TARGAV');
            if (!$plate) { $stats['skipped']++; $bar->advance(); continue; }

            if (Vehicle::where('tenant_id', $this->tenantId)->where('plate', $plate)->exists()) {
                $stats['skipped']++; $bar->advance(); continue;
            }

            // Trova customer_id tramite CF o email del cliente Wincar
            $customerId = null;
            $codCli = (int)($row['F_VEI_CODCLI'] ?? 0);
            if ($codCli && isset($clientMap[$codCli])) {
                $wc  = $clientMap[$codCli];
                $cf  = $this->val($wc, 'F_CODFIS') ?: null;
                $em  = $this->val($wc, 'F__EMAIL') ?: null;
                $customer = Customer::where('tenant_id', $this->tenantId)
                    ->where(function ($q) use ($cf, $em) {
                        if ($cf) $q->orWhere('fiscal_code', $cf);
                        if ($em) $q->orWhere('email', $em);
                    })->first();
                $customerId = $customer?->id;
            }

            // Anno da data immatricolazione
            $datImm = $this->parseDate($row['F_VEI_DATIMM'] ?? '');
            $year   = $datImm ? (int)substr($datImm, 0, 4) : null;

            // Compagnia assicurativa dal nome
            $assName = $this->val($row, 'F_VEI_DEASCL') ?: null;

            if (!$this->dryRun) {
                try {
                    Vehicle::create([
                        'tenant_id'          => $this->tenantId,
                        'customer_id'        => $customerId,
                        'plate'              => strtoupper($plate),
                        'vin'                => $this->val($row, 'F_VEI_TELAIO') ?: null,
                        'brand'              => $this->val($row, 'F_VEI_DESMAR') ?: null,
                        'model'              => $this->val($row, 'F_VEI_DESMOD') ?: null,
                        'version'            => $this->val($row, 'F_VEI_DESVER') ?: null,
                        'year'               => $year,
                        'color'              => $this->val($row, 'F_VEI_DESCOL') ?: null,
                        'insurance_company'  => $assName,
                        'insurance_policy'   => $this->val($row, 'F_VEI_NUMPOL') ?: null,
                        'insurance_expiry'   => $this->parseDate($row['F_VEI_SCAPOL'] ?? ''),
                        'revision_expiry'    => $this->parseDate($row['F_VEI_ULTREV'] ?? ''),
                        'status'             => 'attivo',
                    ]);
                    $stats['imported']++;
                } catch (\Exception $e) {
                    $this->warn(" ⚠️  {$plate}: " . $e->getMessage());
                    $stats['errors']++;
                }
            } else {
                $stats['imported']++;
            }
            $bar->advance();
        }

        $bar->finish();
        $this->stats['Veicoli'] = $stats;
        $this->line("  ✅ {$stats['imported']} importati | ⏭️  {$stats['skipped']} saltati | ❌ {$stats['errors']} errori");
    }

    // ────────────────────────────────────────────────────────────────────────
    // HELPERS
    // ────────────────────────────────────────────────────────────────────────

    private function readMdb(string $file, string $table): array
    {
        $csv = shell_exec('mdb-export ' . escapeshellarg($file) . ' ' . escapeshellarg($table) . ' 2>/dev/null');
        if (!$csv) return [];

        $lines  = explode("\n", trim($csv));
        $header = str_getcsv(array_shift($lines));
        $rows   = [];

        foreach ($lines as $line) {
            if (empty(trim($line))) continue;
            $values = str_getcsv($line);
            // Allinea colonne (alcune righe potrebbero avere colonne extra per virgole nel testo)
            while (count($values) < count($header)) $values[] = '';
            $rows[] = array_combine($header, array_slice($values, 0, count($header)));
        }

        return $rows;
    }

    /** Legge un campo stringa, trimma e restituisce stringa vuota se vuota */
    private function val(array $row, string $key): string
    {
        return trim($row[$key] ?? '');
    }

    /** Legge un campo Memo, pulisce \r\n e restituisce null se vuoto */
    private function memo(array $row, string $key): ?string
    {
        $v = trim(str_replace(["\r\n", "\r"], "\n", $row[$key] ?? ''));
        return $v ?: null;
    }

    /** Converte data Wincar (MM/DD/YY HH:MM:SS) in Y-m-d */
    private function parseDate(string $val): ?string
    {
        $val = trim($val);
        if (!$val) return null;
        try {
            $ts = strtotime($val);
            if ($ts && $ts > 0 && $ts < strtotime('2100-01-01')) {
                return date('Y-m-d', $ts);
            }
        } catch (\Exception $e) {}
        return null;
    }
}