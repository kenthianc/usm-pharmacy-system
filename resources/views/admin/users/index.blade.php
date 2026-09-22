<x-admin-layout active="users">
    <div x-data="{
        showCreateModal: false,
        showEditModal: false,
        editUser: { id: null, name: '', email: '', role: '' }
    }" class="space-y-6">

            <!-- Breadcrumbs / Top Navigation -->
            <div class="flex items-center justify-between">
                <div>
                    <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
                        <a href="{{ route('admin.dashboard') }}" class="hover:text-green-800 font-medium">Admin Hub</a>
                        <span>/</span>
                        <span class="text-slate-800 font-bold">Staff &amp; Users</span>
                    </div>
                    <h1 class="text-2xl font-extrabold text-slate-900 flex items-center gap-2.5">
                        <svg class="w-6 h-6 text-green-800" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                        Clinic Staff &amp; User Accounts
                    </h1>
                </div>

                <button @click="showCreateModal = true" class="inline-flex items-center gap-2 px-4 py-2.5 bg-green-900 hover:bg-green-800 text-yellow-400 text-xs font-bold rounded-xl shadow-md transition border border-green-700 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                    New Staff Account
                </button>
            </div>

            <!-- Flash alerts -->
            @if (session('status'))
                <div class="p-4 rounded-xl bg-emerald-50 border-l-4 border-emerald-600 text-emerald-800 text-sm font-medium shadow-xs flex items-center gap-3">
                    <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            @if (isset($errors) && $errors->any())
                <div class="p-4 rounded-xl bg-rose-50 border-l-4 border-rose-600 text-rose-800 text-sm shadow-xs">
                    <div class="font-bold mb-1">Please correct the following errors:</div>
                    <ul class="list-disc list-inside text-xs space-y-0.5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Search and Filters Bar -->
            <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-200">
                <form method="GET" action="{{ route('admin.users.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3">
                    <div class="sm:col-span-5">
                        <div class="relative">
                            <svg class="w-4 h-4 text-slate-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                            <input type="text" name="search" value="{{ $search }}" placeholder="Search by staff name or email..." class="w-full pl-9 pr-4 py-2 text-xs rounded-xl border-slate-300 focus:border-green-600 focus:ring-green-600 placeholder:text-slate-400">
                        </div>
                    </div>

                    <div class="sm:col-span-3">
                        <select name="role" class="w-full py-2 text-xs rounded-xl border-slate-300 focus:border-green-600 focus:ring-green-600">
                            <option value="">All Roles</option>
                            @foreach($roles as $r)
                                <option value="{{ $r->name }}" {{ $roleFilter === $r->name ? 'selected' : '' }}>
                                    {{ ucfirst(str_replace('_', ' ', $r->name)) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="sm:col-span-2">
                        <select name="status" class="w-full py-2 text-xs rounded-xl border-slate-300 focus:border-green-600 focus:ring-green-600">
                            <option value="">All Statuses</option>
                            <option value="active" {{ $statusFilter === 'active' ? 'selected' : '' }}>Active Only</option>
                            <option value="inactive" {{ $statusFilter === 'inactive' ? 'selected' : '' }}>Inactive Only</option>
                        </select>
                    </div>

                    <div class="sm:col-span-2 flex gap-2">
                        <button type="submit" class="flex-1 py-2 px-3 bg-slate-800 hover:bg-slate-900 text-white text-xs font-bold rounded-xl transition">
                            Filter
                        </button>
                        @if($search || $roleFilter || $statusFilter !== null && $statusFilter !== '')
                            <a href="{{ route('admin.users.index') }}" class="py-2 px-3 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-xl transition text-center">
                                Clear
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Users Table Card -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-slate-700">
                        <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 uppercase tracking-wider text-[11px] font-bold">
                            <tr>
                                <th scope="col" class="py-3.5 px-6">User / Member</th>
                                <th scope="col" class="py-3.5 px-4">Role</th>
                                <th scope="col" class="py-3.5 px-4">Status</th>
                                <th scope="col" class="py-3.5 px-4">Joined Date</th>
                                <th scope="col" class="py-3.5 px-6 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($users as $user)
                                @php
                                    $userRole = $user->roles->pluck('name')->first() ?? 'No Role';
                                @endphp
                                <tr class="hover:bg-slate-50/70 transition">
                                    <td class="py-3.5 px-6">
                                        <div class="flex items-center gap-3">
                                            <div class="w-9 h-9 rounded-full bg-slate-200 text-slate-700 font-bold flex items-center justify-center text-xs shrink-0">
                                                {{ strtoupper(substr($user->name, 0, 2)) }}
                                            </div>
                                            <div>
                                                <div class="font-extrabold text-slate-900 text-sm flex items-center gap-2">
                                                    <span>{{ $user->name }}</span>
                                                    @if($user->id === auth()->id())
                                                        <span class="text-[10px] bg-green-100 text-green-800 font-bold px-1.5 py-0.2 rounded">You</span>
                                                    @endif
                                                </div>
                                                <div class="text-xs text-slate-500 font-mono">{{ $user->email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-3.5 px-4">
                                        @if($userRole === 'admin')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-purple-100 text-purple-800 border border-purple-200">
                                                Administrator
                                            </span>
                                        @elseif($userRole === 'nurse')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                                Nurse
                                            </span>
                                        @elseif($userRole === 'pharmacist')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-100 text-blue-800 border border-blue-200">
                                                Pharmacist
                                            </span>
                                        @elseif($userRole === 'stock_manager')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-200">
                                                Stock Manager
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-slate-100 text-slate-700 border border-slate-200">
                                                Patient
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-4">
                                        @if($user->is_active)
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                Active
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                                Inactive
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-4 text-slate-500">
                                        {{ $user->created_at ? $user->created_at->format('M d, Y') : 'N/A' }}
                                    </td>
                                    <td class="py-3.5 px-6 text-right space-x-2">
                                        <button 
                                            @click="editUser = { id: {{ $user->id }}, name: '{{ addslashes($user->name) }}', email: '{{ addslashes($user->email) }}', role: '{{ $userRole }}' }; showEditModal = true" 
                                            class="px-2.5 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs transition cursor-pointer">
                                            Edit
                                        </button>

                                        @if($user->id !== auth()->id())
                                            <form method="POST" action="{{ route('admin.users.toggle-status', $user) }}" class="inline-block" onsubmit="return confirm('Are you sure you want to {{ $user->is_active ? 'deactivate' : 'activate' }} {{ addslashes($user->name) }}?');">
                                                @csrf
                                                <button type="submit" class="px-2.5 py-1.5 rounded-lg font-semibold text-xs transition cursor-pointer {{ $user->is_active ? 'bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200' : 'bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200' }}">
                                                    {{ $user->is_active ? 'Deactivate' : 'Activate' }}
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-10 text-center text-slate-400">
                                        No users found matching the criteria.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($users->hasPages())
                    <div class="p-4 border-t border-slate-200 bg-slate-50">
                        {{ $users->links() }}
                    </div>
                @endif
            </div>

        </div>

        <!-- Alpine.js Create Staff Modal -->
        <div x-show="showCreateModal" 
             x-cloak 
             class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 p-4"
             @keydown.escape.window="showCreateModal = false">
            <div @click.away="showCreateModal = false" class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-slate-200 space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <h2 class="text-base font-extrabold text-slate-900 flex items-center gap-2">
                        <svg class="w-5 h-5 text-green-800" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" /></svg>
                        Create New Clinic Staff Account
                    </h2>
                    <button @click="showCreateModal = false" class="text-slate-400 hover:text-slate-600 text-lg">&times;</button>
                </div>

                <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Full Name</label>
                        <input type="text" name="name" required placeholder="e.g. Maria Santos, RN" class="w-full text-xs rounded-xl border-slate-300 focus:border-green-600 focus:ring-green-600">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">USM Email Address</label>
                        <input type="email" name="email" required placeholder="e.g. msantos@usm.edu.ph" class="w-full text-xs rounded-xl border-slate-300 focus:border-green-600 focus:ring-green-600">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Assigned Role</label>
                        <select name="role" required class="w-full text-xs rounded-xl border-slate-300 focus:border-green-600 focus:ring-green-600">
                            <option value="nurse">Nurse (Prescriptions & Patient Intake)</option>
                            <option value="pharmacist">Pharmacist (POS & Dispensing)</option>
                            <option value="stock_manager">Stock Manager (Deliveries & Batches)</option>
                            <option value="admin">Administrator (Super-Admin Control)</option>
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Initial Password</label>
                            <input type="password" name="password" required class="w-full text-xs rounded-xl border-slate-300 focus:border-green-600 focus:ring-green-600">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Confirm Password</label>
                            <input type="password" name="password_confirmation" required class="w-full text-xs rounded-xl border-slate-300 focus:border-green-600 focus:ring-green-600">
                        </div>
                    </div>

                    <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-3">
                        <button type="button" @click="showCreateModal = false" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:text-slate-800">
                            Cancel
                        </button>
                        <button type="submit" class="px-5 py-2.5 bg-green-900 hover:bg-green-800 text-yellow-400 text-xs font-bold rounded-xl shadow-md transition">
                            Create Account
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Alpine.js Edit Staff Modal -->
        <div x-show="showEditModal" 
             x-cloak 
             class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 p-4"
             @keydown.escape.window="showEditModal = false">
            <div @click.away="showEditModal = false" class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-slate-200 space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <h2 class="text-base font-extrabold text-slate-900 flex items-center gap-2">
                        <svg class="w-5 h-5 text-green-800" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                        Edit User Account
                    </h2>
                    <button @click="showEditModal = false" class="text-slate-400 hover:text-slate-600 text-lg">&times;</button>
                </div>

                <form :action="'{{ url('admin/users') }}/' + editUser.id" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Full Name</label>
                        <input type="text" name="name" x-model="editUser.name" required class="w-full text-xs rounded-xl border-slate-300 focus:border-green-600 focus:ring-green-600">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Email Address</label>
                        <input type="email" name="email" x-model="editUser.email" required class="w-full text-xs rounded-xl border-slate-300 focus:border-green-600 focus:ring-green-600">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Assigned Role</label>
                        <select name="role" x-model="editUser.role" required class="w-full text-xs rounded-xl border-slate-300 focus:border-green-600 focus:ring-green-600">
                            <option value="nurse">Nurse</option>
                            <option value="pharmacist">Pharmacist</option>
                            <option value="stock_manager">Stock Manager</option>
                            <option value="admin">Administrator</option>
                            <option value="patient">Patient</option>
                        </select>
                    </div>

                    <div class="p-3 bg-amber-50 rounded-xl border border-amber-200 text-amber-800 text-xs">
                        <span class="font-bold">Password Reset (Optional):</span> Leave blank to keep current password unchanged.
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">New Password</label>
                            <input type="password" name="password" class="w-full text-xs rounded-xl border-slate-300 focus:border-green-600 focus:ring-green-600">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Confirm New Password</label>
                            <input type="password" name="password_confirmation" class="w-full text-xs rounded-xl border-slate-300 focus:border-green-600 focus:ring-green-600">
                        </div>
                    </div>

                    <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-3">
                        <button type="button" @click="showEditModal = false" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:text-slate-800">
                            Cancel
                        </button>
                        <button type="submit" class="px-5 py-2.5 bg-green-900 hover:bg-green-800 text-yellow-400 text-xs font-bold rounded-xl shadow-md transition">
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-admin-layout>
