<?php
namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SettingController extends Controller
{
    private function gruppiLabel(): array
    {
        return array_merge(Setting::gruppi(), ['mail' => 'Mail & SMTP']);
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
        \Cache::forget("settings_{$tid}");
        return back()->with('success', 'Impostazioni salvate.');
    }

    public function aggiornaPermessi(Request $request)
    {
        $tid = auth()->user()->tenant_id;
        $userId = $request->user_id;
        $gruppiLabel = $this->gruppiLabel();

        DB::table('setting_permissions')->where('tenant_id', $tid)->where('user_id', $userId)->delete();

        foreach ($gruppiLabel as $gKey => $gLabel) {
            if ($request->input("permessi.{$gKey}")) {
                DB::table('setting_permissions')->insert([
                    'tenant_id' => $tid,
                    'user_id'   => $userId,
                    'gruppo'    => $gKey,
                    'can_edit'  => true,
                    'created_at'=> now(),
                    'updated_at'=> now(),
                ]);
            }
        }
        return back()->with('success', 'Permessi aggiornati.');
    }
}