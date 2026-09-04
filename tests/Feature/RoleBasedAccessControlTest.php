<?php

use App\Models\User;
use Database\Seeders\RoleSeeder;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    $this->seed(RoleSeeder::class);
});

test('unauthenticated users are redirected to login from protected routes', function () {
    $this->get('/prescriptions')->assertRedirect('/login');
    $this->get('/pos')->assertRedirect('/login');
});

test('nurse can access prescription module but cannot access pos module', function () {
    $nurse = User::factory()->create();
    $nurseRole = Role::findByName('nurse');
    $nurse->update(['role_id' => $nurseRole->id]);
    $nurse->assignRole($nurseRole);

    $this->actingAs($nurse)
        ->get('/prescriptions')
        ->assertOk()
        ->assertSee('Prescription Module');

    $this->actingAs($nurse)
        ->get('/pos')
        ->assertForbidden();
});

test('medical secretary can access prescription module but cannot access pos module', function () {
    $secretary = User::factory()->create();
    $secRole = Role::findByName('medical_secretary');
    $secretary->update(['role_id' => $secRole->id]);
    $secretary->assignRole($secRole);

    $this->actingAs($secretary)
        ->get('/prescriptions')
        ->assertOk()
        ->assertSee('Prescription Module');

    $this->actingAs($secretary)
        ->get('/pos')
        ->assertForbidden();
});

test('pharmacist can access pos module but cannot access prescription module', function () {
    $pharmacist = User::factory()->create();
    $pharmRole = Role::findByName('pharmacist');
    $pharmacist->update(['role_id' => $pharmRole->id]);
    $pharmacist->assignRole($pharmRole);

    $this->actingAs($pharmacist)
        ->get('/pos')
        ->assertOk()
        ->assertSee('Pharmacy / POS Module');

    $this->actingAs($pharmacist)
        ->get('/prescriptions')
        ->assertForbidden();
});

test('admin can access both prescription and pos modules', function () {
    $admin = User::factory()->create();
    $adminRole = Role::findByName('admin');
    $admin->update(['role_id' => $adminRole->id]);
    $admin->assignRole($adminRole);

    $this->actingAs($admin)
        ->get('/prescriptions')
        ->assertOk()
        ->assertSee('Prescription Module');

    $this->actingAs($admin)
        ->get('/pos')
        ->assertOk()
        ->assertSee('Pharmacy / POS Module');
});

test('patient and stock manager are forbidden from prescription and pos modules', function () {
    $patientRole = Role::findByName('patient');
    $patient = User::factory()->create(['role_id' => $patientRole->id]);
    $patient->assignRole($patientRole);

    $this->actingAs($patient)->get('/prescriptions')->assertForbidden();
    $this->actingAs($patient)->get('/pos')->assertForbidden();

    $stockRole = Role::findByName('stock_manager');
    $stockManager = User::factory()->create(['role_id' => $stockRole->id]);
    $stockManager->assignRole($stockRole);

    $this->actingAs($stockManager)->get('/prescriptions')->assertForbidden();
    $this->actingAs($stockManager)->get('/pos')->assertForbidden();
});

test('new users registered via breeze are automatically assigned the patient role', function () {
    $response = $this->post('/register', [
        'name' => 'Jane Patient',
        'email' => 'jane.patient@usm.edu.ph',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('dashboard', absolute: false));

    $user = User::where('email', 'jane.patient@usm.edu.ph')->first();
    expect($user)->not->toBeNull()
        ->and($user->hasRole('patient'))->toBeTrue()
        ->and($user->role)->not->toBeNull()
        ->and($user->role->name)->toBe('patient');
});

test('navigation bar displays links according to user role', function () {
    $adminRole = Role::findByName('admin');
    $admin = User::factory()->create(['role_id' => $adminRole->id]);
    $admin->assignRole($adminRole);

    $this->actingAs($admin)
        ->get('/dashboard')
        ->assertSee('Prescriptions')
        ->assertSee('Pharmacy / POS')
        ->assertSee('admin');

    $nurseRole = Role::findByName('nurse');
    $nurse = User::factory()->create(['role_id' => $nurseRole->id]);
    $nurse->assignRole($nurseRole);

    $this->actingAs($nurse)
        ->get('/dashboard')
        ->assertSee('Prescriptions')
        ->assertDontSee('Pharmacy / POS');

    $pharmRole = Role::findByName('pharmacist');
    $pharmacist = User::factory()->create(['role_id' => $pharmRole->id]);
    $pharmacist->assignRole($pharmRole);

    $this->actingAs($pharmacist)
        ->get('/dashboard')
        ->assertDontSee('Prescriptions')
        ->assertSee('Pharmacy / POS');
});
