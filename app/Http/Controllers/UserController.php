<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserAccessLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    private function tid(): int { return Auth::user()->tenant_id; }

    public function index()
    {
        abort_unless(Auth::user()->canDo('utenti.manage'), 403);
        $users = User::forTenant($this->tid())->latest()->get();
        return view('users.index', compact('users'));
    }

    public function create()
    {
        abort_unless(Auth::user()->canDo('utenti.manage'), 403);
        return view('users.form', ['user' => new User()]);
    }

    public function store(Request $request)
    {
        abort_unless(Auth::user()->canDo('utenti.manage'), 403);

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

    public function edit(User $user)
    {
        abort_unless(Auth::user()->canDo('utenti.manage'), 403);
        abort_if($user->tenant_id !== $this->tid(), 403);
        return view('users.form', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        abort_unless(Auth::user()->canDo('utenti.manage'), 403);
        abort_if($user->tenant_id !== $this->tid(), 403);

        $data = $request->validate([
            'name'     => 'required|string|max:100',
            'email'    => 'required|email|unique:users,email,'.$user->id,
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

        // Salva permessi custom
        $customPerms = [];
        foreach (User::ALL_PERMISSIONS as $section => $actions) {
            foreach ($actions as $action => $label) {
                $key = "{$section}.{$action}";
                $customPerms[$key] = $request->boolean("perm_{$section}_{$action}");
            }
        }
        $data['custom_permissions'] = $customPerms;

        $user->update($data);

        return redirect()->route('utenti.index')->with('success', 'Utente aggiornato.');
    }

    public function destroy(User $user)
    {
        abort_unless(Auth::user()->canDo('utenti.manage'), 403);
        abort_if($user->tenant_id !== $this->tid(), 403);
        abort_if($user->id === Auth::id(), 403, 'Non puoi eliminare te stesso.');
        $user->delete();
        return redirect()->route('utenti.index')->with('success', 'Utente eliminato.');
    }

    public function accessLog()
    {
        abort_unless(Auth::user()->canDo('utenti.manage'), 403);
        $logs = UserAccessLog::forTenant($this->tid())
            ->with('user')
            ->orderByDesc('created_at')
            ->paginate(50);
        return view('users.access_log', compact('logs'));
    }

    public function toggleActive(User $user)
    {
        abort_unless(Auth::user()->canDo('utenti.manage'), 403);
        abort_if($user->tenant_id !== $this->tid(), 403);
        $user->update(['active' => !$user->active]);
        return back()->with('success', $user->active ? 'Utente attivato.' : 'Utente disattivato.');
    }
}