<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(RoleSeeder::class);

        $demoUsers = [
            [
                'name' => 'Admin User',
                'email' => 'admin@usm.edu.ph',
                'role' => 'admin',
            ],
            [
                'name' => 'Pharmacist User',
                'email' => 'pharmacist@usm.edu.ph',
                'role' => 'pharmacist',
            ],
            [
                'name' => 'Nurse User',
                'email' => 'nurse@usm.edu.ph',
                'role' => 'nurse',
            ],
            [
                'name' => 'Medical Secretary User',
                'email' => 'medsec@usm.edu.ph',
                'role' => 'medical_secretary',
            ],
            [
                'name' => 'Stock Manager User',
                'email' => 'stockmanager@usm.edu.ph',
                'role' => 'stock_manager',
            ],
            [
                'name' => 'Patient User',
                'email' => 'patient@usm.edu.ph',
                'role' => 'patient',
            ],
        ];

        foreach ($demoUsers as $userData) {
            $role = Role::findByName($userData['role']);

            $user = User::updateOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'password' => Hash::make('password'),
                    'role_id' => $role->id,
                    'email_verified_at' => now(),
                ]
            );

            $user->syncRoles([$role]);
        }

        $this->call([
            PatientSeeder::class,
            MedicineAndBatchSeeder::class,
        ]);
    }
}
