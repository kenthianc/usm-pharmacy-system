<?php

namespace Database\Seeders;

use App\Models\Patient;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class PatientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $patientRole = Role::firstOrCreate(['name' => 'patient', 'guard_name' => 'web']);

        $patientAccounts = [
            [
                'name' => 'Juan Dela Cruz',
                'email' => 'patient@usm.edu.ph',
                'patient_type' => 'student',
                'id_number' => 'USM-2024-00142',
                'contact_number' => '09171234567',
            ],
            [
                'name' => 'Maria Santos',
                'email' => 'maria.santos@usm.edu.ph',
                'patient_type' => 'resident',
                'id_number' => 'RES-2023-0089',
                'contact_number' => '09287654321',
            ],
            [
                'name' => 'Carlo Bautista',
                'email' => 'carlo.bautista@usm.edu.ph',
                'patient_type' => 'student',
                'id_number' => 'USM-2025-01055',
                'contact_number' => '09391122334',
            ],
        ];

        foreach ($patientAccounts as $acc) {
            $user = User::updateOrCreate(
                ['email' => $acc['email']],
                [
                    'name' => $acc['name'],
                    'password' => Hash::make('password'),
                    'role_id' => $patientRole->id,
                    'email_verified_at' => now(),
                ]
            );

            $user->syncRoles([$patientRole]);

            Patient::updateOrCreate(
                ['id_number' => $acc['id_number']],
                [
                    'user_id' => $user->id,
                    'patient_type' => $acc['patient_type'],
                    'contact_number' => $acc['contact_number'],
                ]
            );
        }
    }
}
