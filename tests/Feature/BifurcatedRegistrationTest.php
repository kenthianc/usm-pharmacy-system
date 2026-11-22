<?php

use App\Http\Controllers\Auth\GoogleAuthController;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    Role::firstOrCreate(['name' => 'patient', 'guard_name' => 'web']);
});

test('registration screen can be rendered with bifurcated options', function () {
    $response = $this->get('/register');

    $response->assertStatus(200);
    $response->assertSee('USM Student');
    $response->assertSee('Local Resident');
    $response->assertSee('Continue with USM Google Account');
    $response->assertSee('Complete Barangay / Address');
});

test('local resident can register with full intake demographic fields', function () {
    $response = $this->post('/register', [
        'first_name' => 'Juan',
        'last_name' => 'Dela Cruz',
        'contact_number' => '09123456789',
        'address' => 'Purok 4, Poblacion, Kabacan, Cotabato',
        'date_of_birth' => '1995-06-15',
        'sex' => 'Male',
        'email' => 'juan.delacruz@example.com',
        'password' => 'SecurePass123!',
        'password_confirmation' => 'SecurePass123!',
        'patient_type' => 'resident',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('dashboard', absolute: false));

    $user = User::where('email', 'juan.delacruz@example.com')->first();
    expect($user)->not->toBeNull();
    expect($user->name)->toBe('Juan Dela Cruz');
    expect($user->hasRole('patient'))->toBeTrue();

    $patient = Patient::where('user_id', $user->id)->first();
    expect($patient)->not->toBeNull();
    expect($patient->patient_type)->toBe('resident');
    expect($patient->contact_number)->toBe('09123456789');
    expect($patient->address)->toBe('Purok 4, Poblacion, Kabacan, Cotabato');
    expect($patient->sex)->toBe('Male');
    expect($patient->id_number)->toStartWith('RES-');
});

test('google oauth redirect triggers authorization with usm institutional domain prompt', function () {
    config(['services.google.client_id' => 'mock-google-client-id']);
    config(['services.google.redirect' => 'http://localhost/auth/google/callback']);

    $response = $this->get(route('auth.google.redirect', ['student_id' => '2023-99999']));

    $response->assertStatus(302);
    $location = $response->headers->get('Location');
    expect($location)->toContain('accounts.google.com/o/oauth2/v2/auth');
    expect($location)->toContain('hd=usm.edu.ph');
    expect(session('google_oauth_student_id'))->toBe('2023-99999');
});

test('google oauth callback approves valid student institutional email', function () {
    $controller = new GoogleAuthController;

    $response = $controller->processGoogleUserData([
        'email' => 'maria.clara@student.usm.edu.ph',
        'name' => 'Maria Clara',
        'sub' => '123456789',
    ]);

    expect($response->isRedirect())->toBeTrue();
    expect(Auth::check())->toBeTrue();

    $user = Auth::user();
    expect($user->email)->toBe('maria.clara@student.usm.edu.ph');
    expect($user->name)->toBe('Maria Clara');
    expect($user->hasRole('patient'))->toBeTrue();

    $patient = Patient::where('user_id', $user->id)->first();
    expect($patient)->not->toBeNull();
    expect($patient->patient_type)->toBe('student');
    expect($patient->id_number)->toStartWith('STU-');
});

test('google oauth callback approves usm.edu.ph faculty/student email', function () {
    $controller = new GoogleAuthController;

    $response = $controller->processGoogleUserData([
        'email' => 'pedro.penduko@usm.edu.ph',
        'name' => 'Pedro Penduko',
        'sub' => '987654321',
    ]);

    expect($response->isRedirect())->toBeTrue();
    expect(Auth::check())->toBeTrue();

    $user = Auth::user();
    expect($user->email)->toBe('pedro.penduko@usm.edu.ph');
});

test('google oauth callback rejects unauthorized non-institutional email', function () {
    $controller = new GoogleAuthController;

    $response = $controller->processGoogleUserData([
        'email' => 'random.person@gmail.com',
        'name' => 'Random Person',
        'sub' => '555555555',
    ]);

    expect($response->isRedirect())->toBeTrue();
    expect(session('errors'))->not->toBeNull();
    expect(session('errors')->first('email'))->toContain('Access denied');
    expect(Auth::check())->toBeFalse();
});
