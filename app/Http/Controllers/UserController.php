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

        $data['tenant_id'] = $this->tid();
        $data['password']  = Hash::make($data['password']);
        $data['active']    = $request->boolean('active', true);
        $data['custom_permissions'] = [];

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

        $data['active'] = $request->boolean('active');

        $customPerms = [];
        foreach (User::ALL_PERMISSIONS as $section => $actions) {
            foreach ($actions as $action => $label) {
                $key = "{$section}.{$action}";
                $customPerms[$key] = $request->boolean("perm_{$section}_{$action}");
            }
        }
        $data['custom_permissions'] = $customPerms;

        $utenti->update($data);
        return redirect()->route('utenti.index')->with('success', 'Utente aggiornato.');
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