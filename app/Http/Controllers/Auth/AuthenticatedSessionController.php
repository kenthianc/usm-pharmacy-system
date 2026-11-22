<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\AuditLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $user = $request->user();

        if (! $user->is_active) {
            AuditLog::record(
                'deactivated_login_attempt',
                'auth',
                "Disabled account sign-in rejected for {$user->email}.",
                ['user_id' => $user->id, 'email' => $user->email]
            );

            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()->withErrors([
                'email' => 'This account has been deactivated by the clinic administrator.',
            ]);
        }

        $request->session()->regenerate();

        AuditLog::record(
            'user_login',
            'auth',
            "User {$user->name} ({$user->email}) signed in successfully.",
            ['user_id' => $user->id, 'role' => $user->roles->pluck('name')->first() ?? 'user']
        );

        if ($user->hasRole('admin')) {
            return redirect()->intended(route('admin.dashboard', absolute: false));
        }

        if ($user->hasRole('admission')) {
            return redirect()->intended(route('admission.index', absolute: false));
        }

        if ($user->hasRole('nurse')) {
            return redirect()->intended(route('nurse.dashboard', absolute: false));
        }

        if ($user->hasRole('pharmacist')) {
            return redirect()->intended(route('pos.index', absolute: false));
        }

        if ($user->hasRole('stock_manager')) {
            return redirect()->intended(route('inventory.index', absolute: false));
        }

        if ($user->hasRole('patient')) {
            return redirect()->intended(route('patient.dashboard', absolute: false));
        }

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        if ($user = $request->user()) {
            AuditLog::record(
                'user_logout',
                'auth',
                "User {$user->name} ({$user->email}) signed out.",
                ['user_id' => $user->id]
            );
        }

        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
