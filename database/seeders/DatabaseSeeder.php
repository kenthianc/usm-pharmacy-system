<?php

namespace Database\Seeders;

use App\Models\Patient;
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
                'name' => 'Nurse 001',
                'email' => 'nurse001@usm.edu.ph',
                'password' => 'nurse123',
                'role' => 'nurse',
            ],
            [
                'name' => 'Pharmacist 001',
                'email' => 'pharm001@usm.edu.ph',
                'password' => 'pharm123',
                'role' => 'pharmacist',
            ],
            [
                'name' => 'Stock Keeper 001',
                'email' => 'stock001@usm.edu.ph',
                'password' => 'stock123',
                'role' => 'stock_manager',
            ],
            [
                'name' => 'Patient 001',
                'email' => 'patient001@student.usm.edu.ph',
                'password' => 'patient123',
                'role' => 'patient',
            ],
            [
                'name' => 'Admin User',
                'email' => 'admin@usm.edu.ph',
                'password' => 'admin123',
                'role' => 'admin',
            ],
            [
                'name' => 'Pharmacist User',
                'email' => 'pharmacist@usm.edu.ph',
                'password' => 'password',
                'role' => 'pharmacist',
            ],
            [
                'name' => 'Nurse User',
                'email' => 'nurse@usm.edu.ph',
                'password' => 'password',
                'role' => 'nurse',
            ],
            [
                'name' => 'Stock Manager User',
                'email' => 'stockmanager@usm.edu.ph',
                'password' => 'password',
                'role' => 'stock_manager',
            ],
            [
                'name' => 'Patient User',
                'email' => 'patient@usm.edu.ph',
                'password' => 'password',
                'role' => 'patient',
            ],
        ];

        foreach ($demoUsers as $userData) {
            $role = Role::findByName($userData['role']);

            $user = User::updateOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'password' => Hash::make($userData['password'] ?? 'password'),
                    'role_id' => $role->id,
                    'email_verified_at' => now(),
                ]
            );

            $user->syncRoles([$role]);

            if ($userData['role'] === 'patient') {
                Patient::firstOrCreate(
                    ['user_id' => $user->id],
                    [
                        'id_number' => 'PT-'.str_pad((string) $user->id, 5, '0', STR_PAD_LEFT),
                        'patient_type' => 'student',
                        'contact_number' => '09170000000',
                    ]
                );
            }
        }

        $this->call([
            PatientSeeder::class,
            MedicineAndBatchSeeder::class,
        ]);
    }
}
