<?php
namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    private function gruppiLabel(): array
    {
        return Setting::gruppi();
    }

    public function index()
    {
        $gruppi = $this->gruppiLabel();
        return view('settings.index', compact('gruppi'));
    }

    public function gruppo(string $gruppo)
    {
        $tid = auth()->user()->tenant_id;
        $gruppiLabel = $this->gruppiLabel();
        
        // Recuperiamo i settings esistenti indicizzandoli per 'chiave'
        // Usiamo withoutGlobalScope se il modello Setting ha un filtro automatico sul tenant
        $settings = Setting::withoutGlobalScope('tenant')
            ->where('tenant_id', $tid)
            ->where('gruppo', $gruppo)
            ->get()
            ->keyBy('chiave');

        return view('settings.gruppo', compact('gruppo', 'settings', 'gruppiLabel'));
    }

    public function salva(Request $request, string $gruppo)
    {
        $tid = auth()->user()->tenant_id;

        // ── Gestione upload immagini per sito_web ──────────────────────
        if ($gruppo === 'sito_web') {
            $campiImmagine = ['logo_url', 'logo_favicon', 'seo_og_image', 'hero_immagine', 'chi_siamo_foto'];
            foreach ($campiImmagine as $campo) {
                if ($request->hasFile($campo) && $request->file($campo)->isValid()) {
                    $path = $request->file($campo)->store("sito/{$tid}", 'public');
                    $request->merge([$campo => Storage::url($path)]);
                }
            }
            // Gestione galleria foto
            if ($request->hasFile('galleria_nuove')) {
                $galleria = [];
                $esistente = Setting::withoutGlobalScope('tenant')
                    ->where('tenant_id', $tid)->where('chiave', 'galleria_foto')->value('valore');
                if ($esistente) {
                    $galleria = json_decode($esistente, true) ?? [];
                }
                foreach ($request->file('galleria_nuove') as $foto) {
                    if ($foto->isValid()) {
                        $path = $foto->store("sito/{$tid}/galleria", 'public');
                        $galleria[] = Storage::url($path);
                    }
                }
                $request->merge(['galleria_foto' => json_encode($galleria)]);
            }
            // Rimuovi foto dalla galleria
            if ($request->input('galleria_rimuovi')) {
                $esistente = Setting::withoutGlobalScope('tenant')
                    ->where('tenant_id', $tid)->where('chiave', 'galleria_foto')->value('valore');
                $galleria = json_decode($esistente, true) ?? [];
                $daRimuovere = (array) $request->input('galleria_rimuovi');
                $galleria = array_values(array_diff($galleria, $daRimuovere));
                $request->merge(['galleria_foto' => json_encode($galleria)]);
            }
        }

        $payload = $request->except(['_token', '_method']);

        // ==== Logica speciale per il gruppo AI: rilevamento automatico provider/modello ====
        if ($gruppo === 'ai') {
            $apiKey = trim((string) ($payload['ai_api_key'] ?? ''));
            $model  = trim((string) ($payload['ai_model']   ?? ''));

            // Rileva provider dal prefisso della chiave
            if (str_starts_with($apiKey, 'sk-ant-')) {
                $payload['ai_provider'] = 'anthropic';
                $defaultModel = 'claude-3-5-sonnet-20240620';
            } elseif (str_starts_with($apiKey, 'AIza')) {
                $payload['ai_provider'] = 'google';
                $defaultModel = 'gemini-2.0-flash';
            } else {
                // chiave non riconosciuta: lascia ciò che arriva (o anthropic come fallback)
                $payload['ai_provider'] = $payload['ai_provider'] ?? 'anthropic';
                $defaultModel = $payload['ai_provider'] === 'google' ? 'gemini-2.0-flash' : 'claude-3-5-sonnet-20240620';
            }

            // Se il modello è vuoto, o non coerente col provider rilevato, applica il default
            $low = strtolower($model);
            $coerente = ($payload['ai_provider'] === 'google' && preg_match('/^gemini-\d/', $low))
                     || ($payload['ai_provider'] === 'anthropic' && str_starts_with($low, 'claude-'));
            if ($model === '' || !$coerente) {
                $payload['ai_model'] = $defaultModel;
            } else {
                $payload['ai_model'] = $model;
            }
        }

        foreach ($payload as $chiave => $valore) {
            Setting::withoutGlobalScope('tenant')->updateOrCreate(
                ['tenant_id' => $tid, 'chiave' => $chiave, 'gruppo' => $gruppo],
                ['valore' => $valore]
            );
        }
        Cache::forget("settings_{$tid}");
        return back()->with('success', 'Impostazioni salvate.');
    }

    public function testMail()
    {
        try {
            $host       = Setting::get('mail_host', 'smtp.legalmail.it');
            $port       = Setting::get('mail_port', '587');
            $username   = Setting::get('mail_username', '');
            $password   = Setting::get('mail_password', '');
            $encryption = Setting::get('mail_encryption', 'tls');
            $from       = Setting::get('mail_from_address', $username);
            $fromName   = Setting::get('mail_from_name', 'CarModel');

            if (!$username || !$password) {
                return back()->with('error', 'Configura prima username e password SMTP.');
            }

            config([
                'mail.default'                    => 'smtp',
                'mail.mailers.smtp.host'          => $host,
                'mail.mailers.smtp.port'          => (int) $port,
                'mail.mailers.smtp.username'      => $username,
                'mail.mailers.smtp.password'      => $password,
                'mail.mailers.smtp.encryption'    => $encryption,
                'mail.from.address'               => $from,
                'mail.from.name'                  => $fromName,
            ]);

            Mail::raw('Test mail da CarModel ERP — configurazione SMTP funzionante.', function ($m) use ($from, $fromName) {
                $m->to($from)->subject('✓ Test SMTP CarModel');
            });

            return back()->with('success', '✓ Mail di test inviata a ' . $from);
        } catch (\Exception $e) {
            return back()->with('error', 'Errore SMTP: ' . $e->getMessage());
        }
    }

    public function aggiornaPermessi(Request $request)
    {
        $tid = auth()->user()->tenant_id;
        $userId = $request->user_id;
        $gruppiLabel = $this->gruppiLabel();

        DB::table('setting_permissions')
            ->where('tenant_id', $tid)
            ->where('user_id', $userId)
            ->delete();

        foreach ($gruppiLabel as $gKey => $gLabel) {
            if ($request->input("permessi.{$gKey}")) {
                DB::table('setting_permissions')->insert([
                    'tenant_id'  => $tid,
                    'user_id'    => $userId,
                    'gruppo'     => $gKey,
                    'can_edit'   => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
        return back()->with('success', 'Permessi aggiornati.');
    }
}