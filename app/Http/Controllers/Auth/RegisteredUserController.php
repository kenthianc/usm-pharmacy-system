<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        // Support either split first/last name or single name field
        if ($request->filled('first_name') || $request->filled('last_name')) {
            $request->merge([
                'name' => trim($request->input('first_name', '').' '.$request->input('last_name', '')),
            ]);
        }

        // Support mobile_number as contact_number
        if ($request->filled('mobile_number') && ! $request->filled('contact_number')) {
            $request->merge([
                'contact_number' => $request->input('mobile_number'),
            ]);
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'patient_type' => ['nullable', 'string', 'in:student,faculty,community,walkin,resident'],
            'contact_number' => ['nullable', 'string', 'max:25'],
            'address' => ['nullable', 'string', 'max:500'],
            'date_of_birth' => ['nullable', 'date'],
            'sex' => ['nullable', 'string', 'max:20'],
        ]);

        $patientRole = Role::firstOrCreate([
            'name' => 'patient',
            'guard_name' => 'web',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $patientRole->id,
        ]);

        $user->assignRole($patientRole);

        $patientType = $request->input('patient_type', 'resident');
        $prefix = ($patientType === 'resident') ? 'RES-' : (($patientType === 'student') ? 'STU-' : 'PT-');

        Patient::create([
            'user_id' => $user->id,
            'patient_type' => $patientType,
            'id_number' => $prefix.str_pad((string) $user->id, 5, '0', STR_PAD_LEFT),
            'contact_number' => $request->input('contact_number'),
            'address' => $request->input('address'),
            'date_of_birth' => $request->input('date_of_birth'),
            'sex' => $request->input('sex'),
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
