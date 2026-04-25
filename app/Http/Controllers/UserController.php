<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    private function tid(): int { return Auth::user()->tenant_id; }

    public function index()
    {
        $users = User::forTenant($this->tid())->latest()->get();
        return view('users.index', compact('users'));
    }

    public function create()
    {
        return view('users.form', ['user' => new User()]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:100',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
            'role'     => 'required|in:admin,manager,operatore,vendite',
            'phone'    => 'nullable|string|max:30',
            'notes'    => 'nullable|string',
            'active'   => 'boolean',
        ]);

        $data['tenant_id']          = $this->tid();
        $data['password']           = Hash::make($data['password']);
        $data['active']             = $request->boolean('active', true);
        $data['custom_permissions'] = $this->parsePermissions($request);

        User::create($data);
        return redirect()->route('utenti.index')->with('success', 'Utente creato.');
    }

    public function show(User $utenti)
    {
        return view('users.form', ['user' => $utenti]);
    }

    public function edit(User $utenti)
    {
        return view('users.form', ['user' => $utenti]);
    }

    public function update(Request $request, User $utenti)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:100',
            'email'    => 'required|email|unique:users,email,'.$utenti->id,
            'password' => 'nullable|min:8|confirmed',
            'role'     => 'required|in:admin,manager,operatore,vendite',
            'phone'    => 'nullable|string|max:30',
            'notes'    => 'nullable|string',
            'active'   => 'boolean',
        ]);

        if (empty($data['password'])) {
            unset($data['password']);
        } else {
            $data['password'] = Hash::make($data['password']);
        }

        $data['active']             = $request->boolean('active');
        $data['custom_permissions'] = $this->parsePermissions($request);

        $utenti->update($data);
        return redirect()->route('utenti.index')->with('success', 'Utente aggiornato.');
    }

    /**
     * Estrae i custom_permissions dalle checkbox del form.
     *
     * - Se l'utente NON spunta nessuna checkbox → ritorna null (= usa i default del ruolo)
     * - Se spunta almeno una checkbox → ritorna array con tutte le chiavi
     *   (true per le spuntate, false per le non spuntate)
     *
     * Questo comportamento corrisponde al testo del form:
     *   "Se non spunti nulla, vengono usati i permessi predefiniti del ruolo"
     */
    private function parsePermissions(Request $request): ?array
    {
        $perms = [];
        $any   = false;

        foreach (User::ALL_PERMISSIONS as $section => $actions) {
            foreach ($actions as $action => $label) {
                $field = "perm_{$section}_{$action}";
                $key   = "{$section}.{$action}";
                if ($request->boolean($field)) {
                    $perms[$key] = true;
                    $any = true;
                } else {
                    $perms[$key] = false;
                }
            }
        }

        return $any ? $perms : null;
    }

    public function destroy(User $utenti)
    {
        abort_if($utenti->id === Auth::id(), 403, 'Non puoi eliminare te stesso.');
        $utenti->delete();
        return redirect()->route('utenti.index')->with('success', 'Utente eliminato.');
    }

    public function accessLog()
    {
        return redirect()->route('utenti.index');
    }

    public function toggleActive(User $utenti)
    {
        $utenti->update(['active' => !$utenti->active]);
        return back()->with('success', $utenti->active ? 'Utente attivato.' : 'Utente disattivato.');
    }
}
