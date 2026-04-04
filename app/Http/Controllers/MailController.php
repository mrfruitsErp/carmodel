<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\MailTemplate;

class MailController extends Controller
{
    public function index()
    {
        $tid = auth()->user()->tenant_id;
        $templates = MailTemplate::forTenant($tid)->orderBy('trigger_event')->get();
        $log = DB::table('mail_log')->where('tenant_id', $tid)->orderByDesc('created_at')->limit(20)->get();
        $unread = $log->where('opened_at', null)->where('status', 'sent')->count();
        return view('mail.index', compact('templates', 'log', 'unread'));
    }

    public function create()
    {
        return view('mail.create');
    }

    public function store(Request $request)
    {
        if ($request->to_email && $request->subject) {
            try {
                \Illuminate\Support\Facades\Mail::raw($request->body ?? '', function ($m) use ($request) {
                    $m->to($request->to_email)->subject($request->subject);
                });
                return back()->with('success', 'Mail inviata.');
            } catch (\Exception $e) {
                return back()->with('error', 'Errore invio: ' . $e->getMessage());
            }
        }
        return back()->with('error', 'Dati mancanti.');
    }

    public function show($id) { return back(); }
    public function edit($id) { return back(); }
    public function update(Request $request, $id) { return back(); }
    public function destroy($id) { return back(); }
}