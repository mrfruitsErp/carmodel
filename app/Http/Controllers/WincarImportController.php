<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WincarImportLog;
use Illuminate\Support\Facades\Artisan;

class WincarImportController extends Controller
{
    public function index()
    {
        $tid = auth()->user()->tenant_id;
        $log = WincarImportLog::where('tenant_id', $tid)
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();
        return view('import.wincar', compact('log'));
    }

    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:10240',
            'type' => 'required|in:customers,vehicles,jobs',
        ]);

        $file = $request->file('file');
        $path = $file->store('wincar-imports');
        $tid  = auth()->user()->tenant_id;
        $type = $request->type;

        // Lancia import in background dopo la risposta
        dispatch(function () use ($path, $tid, $type) {
            Artisan::call('wincar:import', [
                'file'     => storage_path('app/' . $path),
                '--tenant' => $tid,
                '--type'   => $type,
            ]);
        })->afterResponse();

        return back()->with('success', 'Import avviato. Ricarica la pagina tra qualche secondo per vedere il risultato.');
    }
}
