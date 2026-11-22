<?php

namespace Database\Seeders;

use App\Models\Admission;
use App\Models\Bed;
use App\Models\Patient;
use App\Models\PatientEndorsement;
use App\Models\User;
use App\Models\Ward;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class HospitalWardAndBedSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admissionStaff = User::whereHas('roles', fn ($q) => $q->where('name', 'admission'))->first()
            ?? User::whereHas('roles', fn ($q) => $q->where('name', 'admin'))->first()
            ?? User::first();

        if (! $admissionStaff) {
            $admissionRole = Role::where('name', 'admission')->first();
            $admissionStaff = User::firstOrCreate(
                ['email' => 'system_admission@usm.edu.ph'],
                [
                    'name' => 'System Admission',
                    'password' => bcrypt('password'),
                    'role_id' => $admissionRole?->id,
                ]
            );
            if ($admissionRole) {
                $admissionStaff->assignRole($admissionRole);
            }
        }

        $nurse = User::whereHas('roles', fn ($q) => $q->where('name', 'nurse'))->first()
            ?? $admissionStaff;

        if (! $nurse) {
            $nurseRole = Role::where('name', 'nurse')->first();
            $nurse = User::firstOrCreate(
                ['email' => 'system_nurse@usm.edu.ph'],
                [
                    'name' => 'System Nurse',
                    'password' => bcrypt('password'),
                    'role_id' => $nurseRole?->id,
                ]
            );
            if ($nurseRole) {
                $nurse->assignRole($nurseRole);
            }
        }

        $wardsData = [
            [
                'ward_name' => 'Emergency & Trauma Complex',
                'ward_type' => 'emergency',
                'nurse_station_assigned' => 'Station 1 - Emergency & Triage',
                'status' => 'active',
                'rooms' => [
                    ['room_number' => 'ER-Bay 1', 'beds' => ['Bed 01', 'Bed 02', 'Bed 03']],
                    ['room_number' => 'ER-Bay 2', 'beds' => ['Bed 04', 'Bed 05', 'Bed 06']],
                ],
            ],
            [
                'ward_name' => 'Male Medical-Surgical Ward',
                'ward_type' => 'inpatient',
                'nurse_station_assigned' => 'Station 2 - Central Inpatient Wing',
                'status' => 'active',
                'rooms' => [
                    ['room_number' => 'Room 101', 'beds' => ['Bed A', 'Bed B', 'Bed C']],
                    ['room_number' => 'Room 102', 'beds' => ['Bed A', 'Bed B', 'Bed C']],
                ],
            ],
            [
                'ward_name' => 'Female Medical-Surgical Ward',
                'ward_type' => 'inpatient',
                'nurse_station_assigned' => 'Station 2 - Central Inpatient Wing',
                'status' => 'active',
                'rooms' => [
                    ['room_number' => 'Room 201', 'beds' => ['Bed A', 'Bed B', 'Bed C']],
                    ['room_number' => 'Room 202', 'beds' => ['Bed A', 'Bed B', 'Bed C']],
                ],
            ],
            [
                'ward_name' => 'Pediatric & University Care Ward',
                'ward_type' => 'pediatric',
                'nurse_station_assigned' => 'Station 3 - Pediatric & OB Wing',
                'status' => 'active',
                'rooms' => [
                    ['room_number' => 'Pedia-A', 'beds' => ['Bed 01', 'Bed 02']],
                    ['room_number' => 'Pedia-B', 'beds' => ['Bed 03', 'Bed 04']],
                ],
            ],
            [
                'ward_name' => 'Maternity & OB-GYN Pavilion',
                'ward_type' => 'maternity',
                'nurse_station_assigned' => 'Station 3 - Pediatric & OB Wing',
                'status' => 'active',
                'rooms' => [
                    ['room_number' => 'OB-Room 1', 'beds' => ['Bed 01', 'Bed 02']],
                    ['room_number' => 'OB-Room 2', 'beds' => ['Bed 03', 'Bed 04']],
                ],
            ],
            [
                'ward_name' => 'Respiratory & Isolation Unit',
                'ward_type' => 'isolation',
                'nurse_station_assigned' => 'Station 4 - Isolation Unit',
                'status' => 'active',
                'rooms' => [
                    ['room_number' => 'ISO-Neg 1', 'beds' => ['Bed 01', 'Bed 02']],
                    ['room_number' => 'ISO-Neg 2', 'beds' => ['Bed 03', 'Bed 04']],
                ],
            ],
        ];

        $createdBeds = [];

        foreach ($wardsData as $wData) {
            $ward = Ward::firstOrCreate(
                ['ward_name' => $wData['ward_name']],
                [
                    'ward_type' => $wData['ward_type'],
                    'nurse_station_assigned' => $wData['nurse_station_assigned'],
                    'status' => $wData['status'],
                ]
            );

            foreach ($wData['rooms'] as $rData) {
                foreach ($rData['beds'] as $bedNum) {
                    $bed = Bed::firstOrCreate(
                        [
                            'ward_id' => $ward->id,
                            'room_number' => $rData['room_number'],
                            'bed_number' => $bedNum,
                        ],
                        [
                            'occupancy_status' => 'available',
                        ]
                    );
                    $createdBeds[] = $bed;
                }
            }
        }

        // Set one bed as under maintenance for realistic dashboard variety
        if (isset($createdBeds[5])) {
            $createdBeds[5]->update(['occupancy_status' => 'maintenance']);
        }

        // Retrieve existing patients or seed sample patients if none exist
        $patients = Patient::take(6)->get();

        if ($patients->isEmpty()) {
            $patients = collect([
                Patient::create([
                    'patient_type' => 'student',
                    'id_number' => 'USM-2024-00128',
                    'contact_number' => '09171234567',
                    'address' => 'Kabacan, Cotabato',
                    'allergies' => 'Penicillin, NSAIDs',
                    'medical_notes' => 'History of bronchial asthma since childhood.',
                ]),
                Patient::create([
                    'patient_type' => 'resident',
                    'id_number' => 'RES-2024-00941',
                    'contact_number' => '09289876543',
                    'address' => 'Poblacion, Mlang, Cotabato',
                    'allergies' => 'None known',
                    'medical_notes' => 'Hypertension Stage 1; maintenance on Amlodipine 5mg.',
                ]),
                Patient::create([
                    'patient_type' => 'student',
                    'id_number' => 'USM-2023-01452',
                    'contact_number' => '09395551234',
                    'address' => 'USM Dormitory Complex, Kabacan',
                    'allergies' => 'Sulfa drugs',
                    'medical_notes' => 'Acute gastroenteritis with mild dehydration.',
                ]),
            ]);
        }

        // Seed 3 realistic active admissions & endorsements
        $clinicalCases = [
            [
                'bed_index' => 0, // ER Bed 01
                'classification' => 'Emergency',
                'respiratory' => 'Nasal Cannula (3 LPM)',
                'complaint' => 'Acute severe dyspnea and wheezing refractory to home Salbutamol inhaler. Bilateral intercostal retractions noted.',
                'vitals' => ['bp' => '130/85', 'temp' => '37.4', 'pulse' => '104', 'rr' => '26', 'spo2' => '93', 'weight' => '54'],
                'notes' => 'Patient endorsed from ER triage for immediate nebulization (Salbutamol+Ipratropium) and continuous pulse oximetry monitoring.',
                'status' => 'pending',
            ],
            [
                'bed_index' => 6, // Room 101 Bed A
                'classification' => 'Inpatient',
                'respiratory' => 'Room Air',
                'complaint' => 'Community-Acquired Pneumonia (Moderate Risk) with 4-day history of high fever, productive rust-colored sputum, and right pleuritic chest pain.',
                'vitals' => ['bp' => '118/76', 'temp' => '38.8', 'pulse' => '92', 'rr' => '22', 'spo2' => '96', 'weight' => '62'],
                'notes' => 'Endorsed to Central Inpatient Station. IV Ceftriaxone 1g initiated in ER. Strict I&O monitoring requested. Fasting blood work scheduled for tomorrow 0600H.',
                'status' => 'acknowledged',
            ],
            [
                'bed_index' => 12, // Room 201 Bed A
                'classification' => 'Observation',
                'respiratory' => 'Room Air',
                'complaint' => 'Acute gastroenteritis with moderate dehydration. 6 episodes of watery non-bloody diarrhea and persistent vomiting following street food ingestion.',
                'vitals' => ['bp' => '100/68', 'temp' => '37.1', 'pulse' => '86', 'rr' => '18', 'spo2' => '99', 'weight' => '48'],
                'notes' => 'Admitted for hydration with Plain Lactated Ringers at 120 mL/hr. Oral rehydration salts tolerated. Discharge planning tomorrow if vomiting ceases.',
                'status' => 'acknowledged',
            ],
        ];

        foreach ($clinicalCases as $idx => $case) {
            if (! isset($patients[$idx]) || ! isset($createdBeds[$case['bed_index']])) {
                continue;
            }

            $patient = $patients[$idx];
            $bed = $createdBeds[$case['bed_index']];

            $admission = Admission::firstOrCreate(
                [
                    'patient_id' => $patient->id,
                    'status' => 'admitted',
                ],
                [
                    'bed_id' => $bed->id,
                    'admitted_by' => $admissionStaff->id,
                    'patient_classification' => $case['classification'],
                    'respiratory_status' => $case['respiratory'],
                    'chief_complaint' => $case['complaint'],
                    'baseline_vitals' => $case['vitals'],
                    'admission_date' => Carbon::now()->subHours(rand(2, 24)),
                ]
            );

            $bed->update(['occupancy_status' => 'occupied']);

            PatientEndorsement::firstOrCreate(
                [
                    'admission_id' => $admission->id,
                ],
                [
                    'endorsed_by' => $admissionStaff->id,
                    'receiving_nurse_id' => $case['status'] === 'acknowledged' ? $nurse->id : null,
                    'nurse_station' => $bed->ward->nurse_station_assigned,
                    'endorsement_notes' => $case['notes'],
                    'acknowledgment_status' => $case['status'],
                    'acknowledged_at' => $case['status'] === 'acknowledged' ? Carbon::now()->subHours(rand(1, 3)) : null,
                ]
            );
        }
    }
}
