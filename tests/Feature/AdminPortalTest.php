<?php

use App\Models\AuditLog;
use App\Models\ClinicSetting;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    $this->seed(RoleSeeder::class);
});

test('admin can access all admin portal pages', function () {
    $adminRole = Role::findByName('admin');
    $admin = User::factory()->create(['role_id' => $adminRole->id]);
    $admin->assignRole($adminRole);

    $this->actingAs($admin)->get(route('admin.dashboard'))->assertOk()->assertSee('USM Clinic Admin Center');
    $this->actingAs($admin)->get(route('admin.users.index'))->assertOk()->assertSee('Clinic Staff & User Accounts');
    $this->actingAs($admin)->get(route('admin.audit-logs'))->assertOk()->assertSee('Compliance Audit Trail');
    $this->actingAs($admin)->get(route('admin.reports'))->assertOk()->assertSee('University Pharmacy & Clinic Analytics');
    $this->actingAs($admin)->get(route('admin.settings.index'))->assertOk()->assertSee('Clinic Operational Settings');
});

test('non-admin roles receive 403 forbidden on admin portal routes', function (string $roleName) {
    $role = Role::findByName($roleName);
    $user = User::factory()->create(['role_id' => $role->id]);
    $user->assignRole($role);

    $this->actingAs($user)->get(route('admin.dashboard'))->assertForbidden();
    $this->actingAs($user)->get(route('admin.users.index'))->assertForbidden();
    $this->actingAs($user)->get(route('admin.audit-logs'))->assertForbidden();
    $this->actingAs($user)->get(route('admin.reports'))->assertForbidden();
    $this->actingAs($user)->get(route('admin.settings.index'))->assertForbidden();
})->with(['nurse', 'pharmacist', 'stock_manager', 'patient']);

test('admin can create a new staff member and syncs role_id with Spatie role', function () {
    $adminRole = Role::findByName('admin');
    $admin = User::factory()->create(['role_id' => $adminRole->id]);
    $admin->assignRole($adminRole);

    $response = $this->actingAs($admin)->post(route('admin.users.store'), [
        'name' => 'Nurse Clara Barton',
        'email' => 'clara.barton@usm.edu.ph',
        'role' => 'nurse',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertRedirect(route('admin.users.index'));
    $response->assertSessionHas('status');

    $createdUser = User::where('email', 'clara.barton@usm.edu.ph')->first();
    expect($createdUser)->not->toBeNull()
        ->and($createdUser->name)->toBe('Nurse Clara Barton')
        ->and($createdUser->hasRole('nurse'))->toBeTrue()
        ->and($createdUser->role_id)->toBe(Role::findByName('nurse')->id)
        ->and($createdUser->is_active)->toBeTrue();

    // Verify audit log
    $log = AuditLog::where('action', 'user_created')->where('module', 'users')->first();
    expect($log)->not->toBeNull()
        ->and($log->user_id)->toBe($admin->id)
        ->and($log->description)->toContain('Nurse Clara Barton');
});

test('admin can edit staff details and update their role', function () {
    $adminRole = Role::findByName('admin');
    $admin = User::factory()->create(['role_id' => $adminRole->id]);
    $admin->assignRole($adminRole);

    $nurseRole = Role::findByName('nurse');
    $staff = User::factory()->create([
        'name' => 'Staff Old Name',
        'email' => 'staff.old@usm.edu.ph',
        'role_id' => $nurseRole->id,
    ]);
    $staff->assignRole($nurseRole);

    $response = $this->actingAs($admin)->put(route('admin.users.update', $staff), [
        'name' => 'Staff New Name',
        'email' => 'staff.new@usm.edu.ph',
        'role' => 'pharmacist',
    ]);

    $response->assertRedirect(route('admin.users.index'));

    $staff->refresh();
    expect($staff->name)->toBe('Staff New Name')
        ->and($staff->email)->toBe('staff.new@usm.edu.ph')
        ->and($staff->hasRole('pharmacist'))->toBeTrue()
        ->and($staff->hasRole('nurse'))->toBeFalse()
        ->and($staff->role_id)->toBe(Role::findByName('pharmacist')->id);

    // Verify audit log
    expect(AuditLog::where('action', 'user_updated')->exists())->toBeTrue();
});

test('admin cannot remove admin role from their own session', function () {
    $adminRole = Role::findByName('admin');
    $admin = User::factory()->create(['role_id' => $adminRole->id]);
    $admin->assignRole($adminRole);

    $response = $this->actingAs($admin)->put(route('admin.users.update', $admin), [
        'name' => 'Admin Self Edit',
        'email' => $admin->email,
        'role' => 'nurse',
    ]);

    $response->assertSessionHasErrors(['role']);
    $admin->refresh();
    expect($admin->hasRole('admin'))->toBeTrue();
});

test('admin can toggle staff active status', function () {
    $adminRole = Role::findByName('admin');
    $admin = User::factory()->create(['role_id' => $adminRole->id]);
    $admin->assignRole($adminRole);

    $nurseRole = Role::findByName('nurse');
    $staff = User::factory()->create(['role_id' => $nurseRole->id, 'is_active' => true]);
    $staff->assignRole($nurseRole);

    // Deactivate
    $response = $this->actingAs($admin)->post(route('admin.users.toggle-status', $staff));
    $response->assertRedirect(route('admin.users.index'));

    $staff->refresh();
    expect($staff->is_active)->toBeFalse();

    // Reactivate
    $this->actingAs($admin)->post(route('admin.users.toggle-status', $staff));
    $staff->refresh();
    expect($staff->is_active)->toBeTrue();
});

test('admin cannot deactivate their own administrative account', function () {
    $adminRole = Role::findByName('admin');
    $admin = User::factory()->create(['role_id' => $adminRole->id, 'is_active' => true]);
    $admin->assignRole($adminRole);

    $response = $this->actingAs($admin)->post(route('admin.users.toggle-status', $admin));
    $response->assertSessionHasErrors(['status_error']);

    $admin->refresh();
    expect($admin->is_active)->toBeTrue();
});

test('deactivated staff user cannot log in', function () {
    $nurseRole = Role::findByName('nurse');
    $staff = User::factory()->create([
        'email' => 'inactive.nurse@usm.edu.ph',
        'password' => Hash::make('password123'),
        'role_id' => $nurseRole->id,
        'is_active' => false,
    ]);
    $staff->assignRole($nurseRole);

    $response = $this->post('/login', [
        'email' => 'inactive.nurse@usm.edu.ph',
        'password' => 'password123',
    ]);

    $this->assertGuest();
    $response->assertSessionHasErrors(['email']);
});

test('admin can update clinic operational settings', function () {
    $adminRole = Role::findByName('admin');
    $admin = User::factory()->create(['role_id' => $adminRole->id]);
    $admin->assignRole($adminRole);

    $response = $this->actingAs($admin)->post(route('admin.settings.update'), [
        'clinic_name' => 'USM Central Health Infirmary',
        'low_stock_threshold' => 25,
        'expiry_warning_days' => 45,
        'contact_number' => '0918-123-4567',
        'campus_location' => 'USM Admin Complex, Kabacan',
        'receipt_footer_note' => 'Official USM Health Services Receipt.',
    ]);

    $response->assertRedirect(route('admin.settings.index'));
    $response->assertSessionHas('status');

    expect(ClinicSetting::get('clinic_name'))->toBe('USM Central Health Infirmary')
        ->and(ClinicSetting::get('low_stock_threshold'))->toBe('25')
        ->and(ClinicSetting::get('expiry_warning_days'))->toBe('45');

    expect(AuditLog::where('action', 'settings_updated')->exists())->toBeTrue();
});
