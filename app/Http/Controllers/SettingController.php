<?php
namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;

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
        foreach ($request->except(['_token', '_method']) as $chiave => $valore) {
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