<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreStaffUserRequest;
use App\Http\Requests\Admin\UpdateStaffUserRequest;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class AdminUserController extends Controller
{
    /**
     * Display a listing of staff and users.
     */
    public function index(Request $request): View
    {
        $search = $request->query('search');
        $roleFilter = $request->query('role');
        $statusFilter = $request->query('status');

        $query = User::with(['roles', 'role'])->latest();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($roleFilter) {
            $query->whereHas('roles', fn ($q) => $q->where('name', $roleFilter));
        }

        if ($statusFilter !== null && $statusFilter !== '') {
            $query->where('is_active', $statusFilter === 'active');
        }

        $users = $query->paginate(15)->withQueryString();
        $roles = Role::where('name', '!=', 'medical_secretary')->get();

        return view('admin.users.index', compact('users', 'roles', 'search', 'roleFilter', 'statusFilter'));
    }

    /**
     * Store a newly created staff member in storage.
     */
    public function store(StoreStaffUserRequest $request): RedirectResponse
    {
        $role = Role::findByName($request->role);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $role->id,
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        $user->assignRole($role);

        AuditLog::record(
            'user_created',
            'users',
            "Created new staff account: {$user->name} ({$role->name})",
            [
                'created_user_id' => $user->id,
                'email' => $user->email,
                'role' => $role->name,
            ]
        );

        return redirect()->route('admin.users.index')
            ->with('status', "Staff account for '{$user->name}' has been created successfully.");
    }

    /**
     * Update the specified user in storage.
     */
    public function update(UpdateStaffUserRequest $request, User $user): RedirectResponse
    {
        // Prevent removing admin role from own account
        if ($user->id === $request->user()->id && $request->role !== 'admin') {
            return back()->withErrors([
                'role' => 'You cannot revoke the administrator role from your own current session.',
            ]);
        }

        $role = Role::findByName($request->role);

        $updateData = [
            'name' => $request->name,
            'email' => $request->email,
            'role_id' => $role->id,
        ];

        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        $user->update($updateData);
        $user->syncRoles([$role]);

        AuditLog::record(
            'user_updated',
            'users',
            "Updated account details for: {$user->name} ({$role->name})",
            [
                'target_user_id' => $user->id,
                'email' => $user->email,
                'role' => $role->name,
                'password_changed' => $request->filled('password'),
            ]
        );

        return redirect()->route('admin.users.index')
            ->with('status', "Account details for '{$user->name}' updated successfully.");
    }

    /**
     * Toggle the active/inactive status of a user.
     */
    public function toggleStatus(Request $request, User $user): RedirectResponse
    {
        if ($user->id === $request->user()->id) {
            return back()->withErrors([
                'status_error' => 'Safety lock: You cannot deactivate your own administrative account.',
            ]);
        }

        $user->is_active = ! $user->is_active;
        $user->save();

        $statusText = $user->is_active ? 'Activated' : 'Deactivated';

        AuditLog::record(
            'user_status_toggled',
            'users',
            "{$statusText} account for: {$user->name} ({$user->email})",
            [
                'target_user_id' => $user->id,
                'new_status' => $user->is_active,
            ]
        );

        return redirect()->route('admin.users.index')
            ->with('status', "Account for '{$user->name}' was {$statusText} successfully.");
    }
}
