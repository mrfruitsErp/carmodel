<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Sinistro;
use App\Models\SinistroRiga;
use App\Models\Lesione;
use App\Models\SinistroAutoSostitutiva;
use App\Models\Customer;
use App\Models\InsuranceCompany;
use App\Models\Expert;
use App\Models\Tenant;

class WincarImportArchivi extends Command
{
    protected $signature = 'wincar:import-archivi
                            {file : Percorso del file wcArchivi.mdb}
                            {--dry-run : Simula senza salvare}
                            {--only= : Importa solo: sinistri, righe, lesioni, sostitutive}
                            {--tenant= : ID tenant (default: 1)}
                            {--limit= : Limita numero record (per test)}';

    protected $description = 'Importa pratiche sinistri da Wincar (wcArchivi.mdb) in CarModel ERP';

    private bool  $dryRun   = false;
    private int   $tenantId = 1;
    private array $stats    = [];

    // Cache per evitare query ripetute
    private array $insuranceCache = [];
    private array $expertCache    = [];
    private array $customerCache  = [];

    public function handle(): int
    {
        $file = $this->argument('file');

        if (!file_exists($file)) {
            $this->error("File non trovato: {$file}");
            return 1;
        }

        exec('which mdb-export 2>/dev/null', $out, $code);
        if ($code !== 0) {
            $this->error('mdbtools non installato. Installa con: apt install mdbtools');
            return 1;
        }

        $this->dryRun   = (bool)$this->option('dry-run');
        $this->tenantId = (int)($this->option('tenant') ?? Tenant::first()?->id ?? 1);
        $only           = $this->option('only');
        $limit          = $this->option('limit') ? (int)$this->option('limit') : null;

        if ($this->dryRun) {
            $this->warn('⚠️  DRY-RUN: nessun dato verrà salvato');
        }

        $this->info("📂 File   : {$file}");
        $this->info("🏢 Tenant : {$this->tenantId}");
        if ($limit) $this->warn("🔢 Limite : {$limit} record");
        $this->newLine();

        // Carica cache assicurazioni e periti già importati
        $this->loadCaches();

        $tasks = [
            'sinistri'    => fn() => $this->importSinistri($file, $limit),
            'righe'       => fn() => $this->importRighe($file, $limit),
            'lesioni'     => fn() => $this->importLesioni($file, $limit),
            'sostitutive' => fn() => $this->importSostitutive($file, $limit),
        ];

        foreach ($tasks as $name => $fn) {
            if ($only && $only !== $name) continue;
            $fn();
            $this->newLine();
        }

        // Riepilogo
        $this->info('══════════════════════════════════════════════════');
        $this->info('  📊 RIEPILOGO IMPORTAZIONE ARCHIVI WINCAR');
        $this->info('══════════════════════════════════════════════════');
        foreach ($this->stats as $label => $s) {
            $this->line(sprintf(
                '  %-25s  ✅ %5d  ⏭️  %5d  ❌ %4d',
                $label, $s['imported'], $s['skipped'], $s['errors']
            ));
        }
        $this->info('══════════════════════════════════════════════════');

        if ($this->dryRun) {
            $this->warn('⚠️  DRY-RUN completato — nessun dato salvato');
        } else {
            $this->info('✅ Importazione completata!');
        }

        return 0;
    }

    // ── SINISTRI (da CARVEI) ─────────────────────────────────────────────────

    private function importSinistri(string $file, ?int $limit): void
    {
        $this->info('🚗 Importo pratiche sinistri (CARVEI)...');
        $rows  = $this->readMdb($file, 'CARVEI', $limit);
        $stats = ['imported' => 0, 'skipped' => 0, 'errors' => 0];
        $bar   = $this->output->createProgressBar(count($rows));
        $bar->start();

        foreach ($rows as $row) {
            $numPra = (int)($row['F_NUMPRA'] ?? 0);
            if (!$numPra) { $stats['skipped']++; $bar->advance(); continue; }

            // Skip se già importato
            if (Sinistro::where('tenant_id', $this->tenantId)->where('wincar_id', $numPra)->exists()) {
                $stats['skipped']++; $bar->advance(); continue;
            }

            // Trova customer_id dal nome/ragione sociale
            $customerId = $this->findCustomerByName($this->val($row, 'F_RAGSOC'));

            // Trova insurance_company_id
            $insuranceId = $this->findInsurance($this->val($row, 'F_DEASCL'));

            // Determina stato
            $stato = $this->mapStato($row);

            if (!$this->dryRun) {
                try {
                    Sinistro::create([
                        'tenant_id'                => $this->tenantId,
                        'customer_id'              => $customerId,
                        'numero_pratica'           => (string)$numPra,
                        'numero_sinistro'          => $this->val($row, 'F_NUMSIN') ?: null,
                        'data_sinistro'            => $this->parseDate($row['F_DATACA'] ?? ''),
                        'data_ingresso'            => $this->parseDate($row['F_DATADS'] ?? '') ?? $this->parseDate($row['F_DATCRE'] ?? ''),
                        'targa'                    => strtoupper($this->val($row, 'F_TARGAV')) ?: null,
                        'marca'                    => $this->val($row, 'F_DESMAR') ?: null,
                        'modello'                  => $this->cleanModello($this->val($row, 'F_DESMOD')),
                        'versione'                 => $this->cleanVersione($this->val($row, 'F_DESVER')),
                        'colore'                   => $this->val($row, 'F_DESCOL') ?: null,
                        'telaio'                   => $this->val($row, 'F_TELAIO') ?: null,
                        'insurance_company_id'     => $insuranceId,
                        'compagnia_assicurazione'  => $this->val($row, 'F_DEASCL') ?: null,
                        'compagnia_controparte'    => $this->val($row, 'F_DEASCO') ?: null,
                        'targa_controparte'        => strtoupper($this->val($row, 'F_TARCON')) ?: null,
                        'veicolo_controparte'      => $this->val($row, 'F_MACCON') ?: null,
                        'conducente_controparte'   => $this->val($row, 'F_NOMECO') ?: null,
                        'tipo_sinistro'            => $this->val($row, 'F_TIPSIN') ?: null,
                        'stato'                    => $stato,
                        'importo_totale'           => (float)($row['F_IMPPRE'] ?? 0),
                        'importo_liquidato'        => (float)($row['F_IMPCON'] ?? 0),
                        'descrizione_danno'        => $this->val($row, 'F_DESLAV') ?: null,
                        'ha_lesioni'               => (int)($row['F_LESCON'] ?? 0) > 0,
                        'ha_auto_sostitutiva'      => false, // aggiornato dopo
                        'foto_count'               => abs((int)($row['F_FOTO'] ?? 0)),
                        'wincar_id'                => $numPra,
                    ]);
                    $stats['imported']++;
                } catch (\Exception $e) {
                    $this->warn(" ⚠️  Pratica {$numPra}: " . $e->getMessage());
                    $stats['errors']++;
                }
            } else {
                $stats['imported']++;
            }
            $bar->advance();
        }

        $bar->finish();
        $this->stats['Pratiche sinistri'] = $stats;
        $this->line("  ✅ {$stats['imported']} importate | ⏭️  {$stats['skipped']} saltate | ❌ {$stats['errors']} errori");
    }

    // ── RIGHE PREVENTIVO (da RIGPRE) ────────────────────────────────────────

    private function importRighe(string $file, ?int $limit): void
    {
        $this->info('📋 Importo righe preventivo (RIGPRE)...');
        $rows  = $this->readMdb($file, 'RIGPRE', $limit ? $limit * 10 : null);
        $stats = ['imported' => 0, 'skipped' => 0, 'errors' => 0];
        $bar   = $this->output->createProgressBar(count($rows));
        $bar->start();

        // Cache sinistri già importati wincar_id → id
        $sinistroMap = Sinistro::where('tenant_id', $this->tenantId)
            ->whereNotNull('wincar_id')
            ->pluck('id', 'wincar_id')
            ->toArray();

        foreach ($rows as $row) {
            $numPra = (int)($row['ID_CODPRE'] ?? 0);
            if (!$numPra) { $stats['skipped']++; $bar->advance(); continue; }

            $sinistroId = $sinistroMap[$numPra] ?? null;
            if (!$sinistroId) { $stats['skipped']++; $bar->advance(); continue; }

            if (!$this->dryRun) {
                try {
                    SinistroRiga::create([
                        'tenant_id'       => $this->tenantId,
                        'sinistro_id'     => $sinistroId,
                        'tipo_riga'       => $this->val($row, 'F_TIPRIG') ?: null,
                        'ordine'          => (int)($row['F_ORDINE'] ?? 0),
                        'codice_articolo' => $this->val($row, 'F_CODART') ?: null,
                        'descrizione'     => $this->val($row, 'F_DESART') ?: null,
                        'posizione'       => $this->val($row, 'F_POSIZ') ?: null,
                        'quantita'        => (float)($row['F_QUANTI'] ?? 1),
                        'prezzo'          => (float)($row['F_PREZZO'] ?? 0),
                        'sconto'          => (float)($row['F_SCONTO'] ?? 0),
                        'tempo_sr'        => (float)($row['F_TEMPSR'] ?? 0),
                        'tempo_la'        => (float)($row['F_TEMPLA'] ?? 0),
                        'tempo_ve'        => (float)($row['F_TEMPVE'] ?? 0),
                        'tempo_me'        => (float)($row['F_TEMPME'] ?? 0),
                        'tipo_ricambio'   => $this->val($row, 'F_TIPRIC') ?: null,
                    ]);
                    $stats['imported']++;
                } catch (\Exception $e) {
                    $stats['errors']++;
                }
            } else {
                $stats['imported']++;
            }
            $bar->advance();
        }

        $bar->finish();
        $this->stats['Righe preventivo'] = $stats;
        $this->line("  ✅ {$stats['imported']} importate | ⏭️  {$stats['skipped']} saltate | ❌ {$stats['errors']} errori");
    }

    // ── LESIONI (da LESIONI) ─────────────────────────────────────────────────

    private function importLesioni(string $file, ?int $limit): void
    {
        $this->info('🏥 Importo lesioni personali (LESIONI)...');
        $rows  = $this->readMdb($file, 'LESIONI', $limit);
        $stats = ['imported' => 0, 'skipped' => 0, 'errors' => 0];
        $bar   = $this->output->createProgressBar(count($rows));
        $bar->start();

        $sinistroMap = Sinistro::where('tenant_id', $this->tenantId)
            ->whereNotNull('wincar_id')
            ->pluck('id', 'wincar_id')
            ->toArray();

        foreach ($rows as $row) {
            $numPra   = (int)($row['F_LES_NUMPRA'] ?? 0);
            $wincarId = (int)($row['F_LES_CODLES'] ?? 0);

            $sinistroId = $sinistroMap[$numPra] ?? null;
            if (!$sinistroId) { $stats['skipped']++; $bar->advance(); continue; }

            if (Lesione::where('tenant_id', $this->tenantId)->where('wincar_id', $wincarId)->exists()) {
                $stats['skipped']++; $bar->advance(); continue;
            }

            if (!$this->dryRun) {
                try {
                    Lesione::create([
                        'tenant_id'           => $this->tenantId,
                        'sinistro_id'         => $sinistroId,
                        'nome'                => $this->val($row, 'F_LES___NOME') ?: null,
                        'indirizzo'           => $this->val($row, 'F_LES_INDIRI') ?: null,
                        'citta'               => $this->val($row, 'F_LES__CITTA') ?: null,
                        'cap'                 => $this->val($row, 'F_LES____CAP') ?: null,
                        'provincia'           => $this->val($row, 'F_LES_PROVIN') ?: null,
                        'telefono'            => $this->val($row, 'F_LES___TEL1') ?: null,
                        'telefono2'           => $this->val($row, 'F_LES___TEL2') ?: null,
                        'email'               => $this->val($row, 'F_LES__EMAIL') ?: null,
                        'professione'         => $this->val($row, 'F_LES_PROFES') ?: null,
                        'data_referto'        => $this->parseDate($row['F_LES_REFERT'] ?? ''),
                        'giorni_referto'      => (int)($row['F_LES_GGREFE'] ?? 0) ?: null,
                        'data_guarigione'     => $this->parseDate($row['F_LES_DATGUA'] ?? ''),
                        'giorni_temporanea'   => (int)($row['F_LES_GGTEMP'] ?? 0) ?: null,
                        'postumi'             => (bool)($row['F_LES_POSTUM'] ?? false),
                        'percentuale_postumi' => (int)($row['F_LES_PERPOS'] ?? 0) ?: null,
                        'ospedale'            => $this->val($row, 'F_LES_RICOVE') ?: null,
                        'ricovero_dal'        => $this->parseDate($row['F_LES_RICDAL'] ?? ''),
                        'ricovero_al'         => $this->parseDate($row['F_LES__RICAL'] ?? ''),
                        'medico_legale'       => (bool)($row['F_LES_MEDLEG'] ?? false),
                        'nome_medico'         => $this->val($row, 'F_LES_NOMMED') ?: null,
                        'totale_spese'        => (float)($row['F_LES_TOTSPE'] ?? 0),
                        'importo_offerta'     => (float)($row['F_LES_IMPOFF'] ?? 0),
                        'importo_concordato'  => (float)($row['F_LES_IMPCON'] ?? 0),
                        'stato'               => $this->val($row, 'F_LES__STATO') ?: null,
                        'note'                => $this->memo($row, 'F_LES___NOTE'),
                        'wincar_id'           => $wincarId ?: null,
                    ]);

                    // Aggiorna flag ha_lesioni sul sinistro
                    Sinistro::where('id', $sinistroId)->update(['ha_lesioni' => true]);

                    $stats['imported']++;
                } catch (\Exception $e) {
                    $this->warn(" ⚠️  Lesione pratica {$numPra}: " . $e->getMessage());
                    $stats['errors']++;
                }
            } else {
                $stats['imported']++;
            }
            $bar->advance();
        }

        $bar->finish();
        $this->stats['Lesioni personali'] = $stats;
        $this->line("  ✅ {$stats['imported']} importate | ⏭️  {$stats['skipped']} saltate | ❌ {$stats['errors']} errori");
    }

    // ── AUTO SOSTITUTIVE ─────────────────────────────────────────────────────

    private function importSostitutive(string $file, ?int $limit): void
    {
        $this->info('🚙 Importo auto sostitutive (SostitutiveAuto)...');
        $rows  = $this->readMdb($file, 'SostitutiveAuto', $limit);
        $stats = ['imported' => 0, 'skipped' => 0, 'errors' => 0];
        $bar   = $this->output->createProgressBar(count($rows));
        $bar->start();

        $sinistroMap = Sinistro::where('tenant_id', $this->tenantId)
            ->whereNotNull('wincar_id')
            ->pluck('id', 'wincar_id')
            ->toArray();

        foreach ($rows as $row) {
            $codPra   = (int)($row['F_SOS_CODPRA'] ?? 0);
            $wincarId = (int)($row['F_SOS_CODSOS'] ?? 0);

            $sinistroId = $sinistroMap[$codPra] ?? null;
            if (!$sinistroId) { $stats['skipped']++; $bar->advance(); continue; }

            if (SinistroAutoSostitutiva::where('tenant_id', $this->tenantId)->where('wincar_id', $wincarId)->exists()) {
                $stats['skipped']++; $bar->advance(); continue;
            }

            if (!$this->dryRun) {
                try {
                    SinistroAutoSostitutiva::create([
                        'tenant_id'       => $this->tenantId,
                        'sinistro_id'     => $sinistroId,
                        'targa'           => strtoupper($this->val($row, 'F_SOS__TARGA')) ?: null,
                        'marca_modello'   => $this->val($row, 'F_SOS_MARMOD') ?: null,
                        'telaio'          => $this->val($row, 'F_SOS_TELAIO') ?: null,
                        'gruppo'          => $this->val($row, 'F_SOS_GRUPPO') ?: null,
                        'data_inizio'     => $this->parseDate($row['F_SOS_DATINI'] ?? ''),
                        'data_fine'       => $this->parseDate($row['F_SOS_DATFIN'] ?? ''),
                        'km_inizio'       => (int)($row['F_SOS_KIMINI'] ?? 0) ?: null,
                        'km_fine'         => (int)($row['F_SOS_KIMFIN'] ?? 0) ?: null,
                        'costo'           => (float)($row['F_SOS__COSTO'] ?? 0),
                        'numero_noleggio' => $this->val($row, 'F_SOS_NUMNOL') ?: null,
                        'autorizzazione'  => $this->val($row, 'F_SOS_AUTORI') ?: null,
                        'conducente'      => $this->val($row, 'F_SOS_NOMGUI') ?: null,
                        'fornitore'       => $this->val($row, 'F_SOS_FORNIT') ?: null,
                        'motivo'          => $this->memo($row, 'F_SOS_MOTIVO'),
                        'wincar_id'       => $wincarId ?: null,
                    ]);

                    // Aggiorna flag sul sinistro
                    Sinistro::where('id', $sinistroId)->update(['ha_auto_sostitutiva' => true]);

                    $stats['imported']++;
                } catch (\Exception $e) {
                    $stats['errors']++;
                }
            } else {
                $stats['imported']++;
            }
            $bar->advance();
        }

        $bar->finish();
        $this->stats['Auto sostitutive'] = $stats;
        $this->line("  ✅ {$stats['imported']} importate | ⏭️  {$stats['skipped']} saltate | ❌ {$stats['errors']} errori");
    }

    // ── HELPERS ──────────────────────────────────────────────────────────────

    private function loadCaches(): void
    {
        $this->info('⏳ Carico cache...');

        InsuranceCompany::where('tenant_id', $this->tenantId)
            ->get(['id', 'name'])
            ->each(fn($i) => $this->insuranceCache[strtolower(trim($i->name))] = $i->id);

        Expert::where('tenant_id', $this->tenantId)
            ->get(['id', 'name'])
            ->each(fn($e) => $this->expertCache[strtolower(trim($e->name))] = $e->id);

        Customer::where('tenant_id', $this->tenantId)
            ->get(['id', 'first_name', 'last_name', 'company_name'])
            ->each(function ($c) {
                $full = strtolower(trim($c->last_name . ' ' . $c->first_name));
                $this->customerCache[$full] = $c->id;
                if ($c->company_name) {
                    $this->customerCache[strtolower(trim($c->company_name))] = $c->id;
                }
            });

        $this->info('  Assicurazioni: ' . count($this->insuranceCache));
        $this->info('  Esperti: ' . count($this->expertCache));
        $this->info('  Clienti: ' . count($this->customerCache));
        $this->newLine();
    }

    private function findInsurance(string $name): ?int
    {
        if (!$name) return null;
        return $this->insuranceCache[strtolower(trim($name))] ?? null;
    }

    private function findCustomerByName(string $ragSoc): ?int
    {
        if (!$ragSoc) return null;
        $key = strtolower(trim($ragSoc));
        return $this->customerCache[$key] ?? null;
    }

    private function mapStato(array $row): string
    {
        $chius = (int)($row['F_CHIUS2'] ?? 0);
        $stapre = $this->val($row, 'F_STAPRE');

        if ($chius > 0) return 'chiuso';
        if ($stapre === 'S') return 'in_lavorazione';
        return 'aperto';
    }

    private function cleanModello(string $val): ?string
    {
        if (!$val) return null;
        // Rimuove "dal 01/2010 fino al 06/2015" dal nome modello
        $clean = preg_replace('/\s+(dal|fino al|from|to)\s+[\d\/]+.*/i', '', $val);
        return trim($clean) ?: $val;
    }

    private function cleanVersione(string $val): ?string
    {
        if (!$val) return null;
        // Rimuove "Berlina 5 Porte" lasciando solo dati tecnici
        return trim($val) ?: null;
    }

    private function readMdb(string $file, string $table, ?int $limit = null): array
    {
        $csv = shell_exec('mdb-export ' . escapeshellarg($file) . ' ' . escapeshellarg($table) . ' 2>/dev/null');
        if (!$csv) return [];

        $lines  = explode("\n", trim($csv));
        $header = str_getcsv(array_shift($lines));
        $rows   = [];
        $count  = 0;

        foreach ($lines as $line) {
            if (empty(trim($line))) continue;
            if ($limit && $count >= $limit) break;
            $values = str_getcsv($line);
            while (count($values) < count($header)) $values[] = '';
            $rows[] = array_combine($header, array_slice($values, 0, count($header)));
            $count++;
        }

        return $rows;
    }

    private function val(array $row, string $key): string
    {
        return trim($row[$key] ?? '');
    }

    private function memo(array $row, string $key): ?string
    {
        $v = trim(str_replace(["\r\n", "\r"], "\n", $row[$key] ?? ''));
        return $v ?: null;
    }

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