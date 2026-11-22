<?php

namespace Database\Seeders;

use App\Models\Admission;
use App\Models\AuditLog;
use App\Models\Bed;
use App\Models\Delivery;
use App\Models\DeliveryItem;
use App\Models\Medicine;
use App\Models\Patient;
use App\Models\PatientBill;
use App\Models\PatientEndorsement;
use App\Models\PosTransaction;
use App\Models\PosTransactionItem;
use App\Models\Prescription;
use App\Models\PrescriptionItem;
use App\Models\StockBatch;
use App\Models\User;
use App\Models\Ward;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class ComprehensiveDemoDataSeeder extends Seeder
{
    /**
     * Run the comprehensive demo database seed.
     */
    public function run(): void
    {
        // 1. Fetch key staff users
        $admin = User::whereHas('roles', fn ($q) => $q->where('name', 'admin'))->first() ?? User::first();
        $admissionStaff = User::whereHas('roles', fn ($q) => $q->where('name', 'admission'))->first() ?? $admin;
        $nurse = User::whereHas('roles', fn ($q) => $q->where('name', 'nurse'))->first() ?? $admin;
        $pharmacist = User::whereHas('roles', fn ($q) => $q->where('name', 'pharmacist'))->first() ?? $admin;
        $stockManager = User::whereHas('roles', fn ($q) => $q->where('name', 'stock_manager'))->first() ?? $admin;
        $patientRole = Role::findByName('patient');

        // 2. Comprehensive Patients across all USM classifications
        $patientData = [
            [
                'name' => 'Juan Dela Cruz',
                'email' => 'juan.delacruz@student.usm.edu.ph',
                'patient_type' => 'student',
                'id_number' => 'USM-2024-00142',
                'contact_number' => '09171234567',
                'emergency_contact' => 'Rosa Dela Cruz (Mother) - 09171112233',
                'address' => 'USM Men Dormitory Complex, Kabacan, Cotabato',
                'sex' => 'male',
                'date_of_birth' => Carbon::now()->subYears(20)->toDateString(),
                'allergies' => 'Penicillin, NSAIDs',
                'medical_notes' => 'Known case of bronchial asthma with occasional seasonal wheezing.',
            ],
            [
                'name' => 'Maria Santos',
                'email' => 'maria.santos@usm.edu.ph',
                'patient_type' => 'resident',
                'id_number' => 'RES-2023-0089',
                'contact_number' => '09287654321',
                'emergency_contact' => 'Roberto Santos (Husband) - 09289998877',
                'address' => 'Poblacion, Kabacan, Cotabato',
                'sex' => 'female',
                'date_of_birth' => Carbon::now()->subYears(45)->toDateString(),
                'allergies' => 'None known',
                'medical_notes' => 'Hypertension Stage 1; maintenance on Amlodipine 5mg OD.',
            ],
            [
                'name' => 'Carlo Bautista',
                'email' => 'carlo.bautista@student.usm.edu.ph',
                'patient_type' => 'student',
                'id_number' => 'USM-2025-01055',
                'contact_number' => '09391122334',
                'emergency_contact' => 'Elena Bautista (Mother) - 09395554433',
                'address' => 'Brgy. Osias, Kabacan, Cotabato',
                'sex' => 'male',
                'date_of_birth' => Carbon::now()->subYears(19)->toDateString(),
                'allergies' => 'Sulfa drugs',
                'medical_notes' => 'Acute laceration on right forearm secondary to lab apparatus breakage.',
            ],
            [
                'name' => 'Prof. Corazon Aquino-Reyes',
                'email' => 'corazon.reyes@usm.edu.ph',
                'patient_type' => 'employee',
                'id_number' => 'EMP-2018-0034',
                'contact_number' => '09187776655',
                'emergency_contact' => 'Dr. Arthur Reyes (Spouse) - 09185554433',
                'address' => 'USM Faculty Housing Complex, Kabacan',
                'sex' => 'female',
                'date_of_birth' => Carbon::now()->subYears(52)->toDateString(),
                'allergies' => 'Aspirin, Ibuprofen',
                'medical_notes' => 'Faculty, College of Agriculture. Type 2 Diabetes Mellitus; UTI workup.',
            ],
            [
                'name' => 'Eduardo Mendoza',
                'email' => 'eduardo.mendoza@student.usm.edu.ph',
                'patient_type' => 'student',
                'id_number' => 'USM-2026-07777',
                'contact_number' => '09172223344',
                'emergency_contact' => 'Felipe Mendoza (Father) - 09179991122',
                'address' => 'Purok 4, Kayaga, Kabacan',
                'sex' => 'male',
                'date_of_birth' => Carbon::now()->subYears(21)->toDateString(),
                'allergies' => 'None known',
                'medical_notes' => 'College of Engineering student. Community-Acquired Pneumonia (Moderate Risk).',
            ],
            [
                'name' => 'Engr. Noel Dizon',
                'email' => 'noel.dizon@usm.edu.ph',
                'patient_type' => 'employee',
                'id_number' => 'EMP-2015-0112',
                'contact_number' => '09198887766',
                'emergency_contact' => 'Clara Dizon (Wife) - 09193332211',
                'address' => 'Staff Housing Unit 12, USM Campus, Kabacan',
                'sex' => 'male',
                'date_of_birth' => Carbon::now()->subYears(41)->toDateString(),
                'allergies' => 'None known',
                'medical_notes' => 'Faculty Engineer. Post-appendectomy Day 1, surgical wound clean and dry.',
            ],
            [
                'name' => 'Princess Althea Dimaculangan',
                'email' => 'althea.dimaculangan@student.usm.edu.ph',
                'patient_type' => 'student',
                'id_number' => 'USM-2024-03211',
                'contact_number' => '09204443322',
                'emergency_contact' => 'Maricel Dimaculangan (Mother) - 09207776655',
                'address' => 'USM Ladies Dorm 2, Kabacan',
                'sex' => 'female',
                'date_of_birth' => Carbon::now()->subYears(18)->toDateString(),
                'allergies' => 'Aspirin',
                'medical_notes' => 'Acute febrile respiratory illness; under droplet and airborne isolation protocols.',
            ],
            [
                'name' => 'Baby Lucas Santos',
                'email' => 'lucas.santos@usm.edu.ph',
                'patient_type' => 'resident',
                'id_number' => 'PED-2026-0045',
                'contact_number' => '09287654321',
                'emergency_contact' => 'Maria Santos (Mother) - 09287654321',
                'address' => 'Poblacion, Kabacan, Cotabato',
                'sex' => 'male',
                'date_of_birth' => Carbon::now()->subYears(3)->toDateString(),
                'allergies' => 'None known',
                'medical_notes' => 'Pediatric patient. Acute bronchitis with productive cough and mild pyrexia.',
            ],
            [
                'name' => 'Clarissa Mae Soriano',
                'email' => 'clarissa.soriano@usm.edu.ph',
                'patient_type' => 'resident',
                'id_number' => 'MAT-2026-0088',
                'contact_number' => '09307778899',
                'emergency_contact' => 'Danilo Soriano (Husband) - 09305551122',
                'address' => 'Brgy. Malamote, Kabacan',
                'sex' => 'female',
                'date_of_birth' => Carbon::now()->subYears(28)->toDateString(),
                'allergies' => 'None known',
                'medical_notes' => 'Primigravida 39 weeks AOG in active labor. Fetal heart tones stable.',
            ],
            [
                'name' => 'Atty. Manuel Quezon',
                'email' => 'manuel.quezon@usm.edu.ph',
                'patient_type' => 'resident',
                'id_number' => 'RES-2026-0155',
                'contact_number' => '09223334455',
                'emergency_contact' => 'Aurora Quezon (Wife) - 09227778899',
                'address' => 'Crossing Datu Mantil, Kabacan',
                'sex' => 'male',
                'date_of_birth' => Carbon::now()->subYears(68)->toDateString(),
                'allergies' => 'Ciprofloxacin',
                'medical_notes' => 'Senior Citizen resident. Routine medical checkup and prescription maintenance.',
            ],
        ];

        $patientModels = [];
        foreach ($patientData as $p) {
            $user = User::updateOrCreate(
                ['email' => $p['email']],
                [
                    'name' => $p['name'],
                    'password' => Hash::make('password'),
                    'role_id' => $patientRole->id,
                    'email_verified_at' => now(),
                ]
            );
            $user->syncRoles([$patientRole]);

            $patientModel = Patient::updateOrCreate(
                ['id_number' => $p['id_number']],
                [
                    'user_id' => $user->id,
                    'name' => $p['name'],
                    'patient_type' => $p['patient_type'],
                    'contact_number' => $p['contact_number'],
                    'emergency_contact' => $p['emergency_contact'],
                    'address' => $p['address'],
                    'sex' => $p['sex'],
                    'date_of_birth' => $p['date_of_birth'],
                    'allergies' => $p['allergies'],
                    'medical_notes' => $p['medical_notes'],
                ]
            );
            $patientModels[$p['id_number']] = $patientModel;
        }

        // 3. Populate Ward Beds with clinical cases
        $wards = Ward::with('beds')->get()->keyBy('ward_name');

        // Helper to find bed
        $getBed = function (string $wardName, string $roomNumber, string $bedNumber) use ($wards) {
            $ward = $wards[$wardName] ?? null;
            if (! $ward) {
                return null;
            }

            return $ward->beds->first(fn ($b) => $b->room_number === $roomNumber && $b->bed_number === $bedNumber);
        };

        // Comprehensive active admissions across wards
        $admissionsConfig = [
            // ER
            [
                'patient_key' => 'USM-2024-00142',
                'ward' => 'Emergency & Trauma Complex',
                'room' => 'ER-Bay 1',
                'bed' => 'Bed 01',
                'classification' => 'Emergency',
                'respiratory' => 'Nasal Cannula (3 LPM)',
                'complaint' => 'Acute severe dyspnea and bronchospasm refractory to home inhaler. Intercostal retractions noted.',
                'vitals' => ['bp' => '130/85', 'temp' => '37.4', 'pulse' => '104', 'rr' => '26', 'spo2' => '93', 'weight' => '54'],
                'notes' => 'Patient endorsed from ER triage for emergency bronchodilator nebulization and telemetry monitoring.',
                'endorsement_status' => 'pending',
                'hours_ago' => 2,
            ],
            [
                'patient_key' => 'USM-2025-01055',
                'ward' => 'Emergency & Trauma Complex',
                'room' => 'ER-Bay 2',
                'bed' => 'Bed 04',
                'classification' => 'Emergency',
                'respiratory' => 'Room Air',
                'complaint' => 'Moderate laceration on right forearm secondary to lab beaker breakage. Hemostasis achieved.',
                'vitals' => ['bp' => '115/75', 'temp' => '36.8', 'pulse' => '78', 'rr' => '18', 'spo2' => '99', 'weight' => '65'],
                'notes' => 'Wound cleaned and sutured (5 stitches). Antitetanus prophylaxis administered. For discharge post-observation.',
                'endorsement_status' => 'acknowledged',
                'hours_ago' => 4,
            ],
            // Male Med-Surg Ward
            [
                'patient_key' => 'USM-2026-07777',
                'ward' => 'Male Medical-Surgical Ward',
                'room' => 'Room 101',
                'bed' => 'Bed A',
                'classification' => 'Inpatient',
                'respiratory' => 'Room Air',
                'complaint' => 'Community-Acquired Pneumonia (Moderate Risk) with 4-day history of high fever, productive cough, and right pleuritic chest pain.',
                'vitals' => ['bp' => '120/80', 'temp' => '38.6', 'pulse' => '88', 'rr' => '20', 'spo2' => '97', 'weight' => '62'],
                'notes' => 'Endorsed to Central Inpatient Wing. IV antibiotics ongoing. Monitor vitals Q4H. Collect sputum for culture.',
                'endorsement_status' => 'acknowledged',
                'hours_ago' => 16,
            ],
            [
                'patient_key' => 'EMP-2015-0112',
                'ward' => 'Male Medical-Surgical Ward',
                'room' => 'Room 102',
                'bed' => 'Bed A',
                'classification' => 'Inpatient',
                'respiratory' => 'Room Air',
                'complaint' => 'Post-open appendectomy Day 1. Surgical wound intact and dry. Tolerating soft diet.',
                'vitals' => ['bp' => '125/82', 'temp' => '36.9', 'pulse' => '74', 'rr' => '16', 'spo2' => '98', 'weight' => '72'],
                'notes' => 'Encourage early ambulation. Oral analgesics as scheduled. Flatus noted.',
                'endorsement_status' => 'acknowledged',
                'hours_ago' => 28,
            ],
            // Female Med-Surg Ward
            [
                'patient_key' => 'RES-2023-0089',
                'ward' => 'Female Medical-Surgical Ward',
                'room' => 'Room 201',
                'bed' => 'Bed A',
                'classification' => 'Observation',
                'respiratory' => 'Room Air',
                'complaint' => 'Acute gastroenteritis with mild dehydration secondary to contaminated food intake.',
                'vitals' => ['bp' => '110/70', 'temp' => '37.1', 'pulse' => '80', 'rr' => '18', 'spo2' => '99', 'weight' => '52'],
                'notes' => 'Hydrated with Plain Lactated Ringers. Vomiting resolved. ORS sachet ongoing.',
                'endorsement_status' => 'acknowledged',
                'hours_ago' => 12,
            ],
            [
                'patient_key' => 'EMP-2018-0034',
                'ward' => 'Female Medical-Surgical Ward',
                'room' => 'Room 202',
                'bed' => 'Bed A',
                'classification' => 'Inpatient',
                'respiratory' => 'Room Air',
                'complaint' => 'Uncontrolled Type 2 Diabetes Mellitus with pyelonephritis symptoms (dysuria, bilateral flank pain, fever).',
                'vitals' => ['bp' => '135/88', 'temp' => '38.4', 'pulse' => '84', 'rr' => '18', 'spo2' => '98', 'weight' => '68'],
                'notes' => 'Sliding-scale regular insulin protocol active. Strict input and output monitoring.',
                'endorsement_status' => 'acknowledged',
                'hours_ago' => 36,
            ],
            // Pediatric Ward
            [
                'patient_key' => 'PED-2026-0045',
                'ward' => 'Pediatric & University Care Ward',
                'room' => 'Pedia-A',
                'bed' => 'Bed 01',
                'classification' => 'Inpatient',
                'respiratory' => 'Room Air',
                'complaint' => 'Pediatric acute bronchitis with barking cough, low-grade fever, and decreased appetite for 3 days.',
                'vitals' => ['bp' => '90/60', 'temp' => '38.1', 'pulse' => '110', 'rr' => '24', 'spo2' => '98', 'weight' => '14'],
                'notes' => 'Comfortable in mother arms. Oral antipyretic given. Ensure adequate hydration with warm fluids.',
                'endorsement_status' => 'acknowledged',
                'hours_ago' => 8,
            ],
            // Maternity
            [
                'patient_key' => 'MAT-2026-0088',
                'ward' => 'Maternity & OB-GYN Pavilion',
                'room' => 'OB-Room 1',
                'bed' => 'Bed 01',
                'classification' => 'Inpatient',
                'respiratory' => 'Room Air',
                'complaint' => 'Gravida 1 Para 0, 39 weeks AOG in active labor. Cervical dilation 5cm, 80% effaced. Intact membranes.',
                'vitals' => ['bp' => '120/75', 'temp' => '36.8', 'pulse' => '84', 'rr' => '18', 'spo2' => '99', 'weight' => '66'],
                'notes' => 'Continuous external fetal monitoring. Uterine contractions moderate every 3-4 minutes. Dr. Santos attending.',
                'endorsement_status' => 'acknowledged',
                'hours_ago' => 3,
            ],
            // Isolation
            [
                'patient_key' => 'USM-2024-03211',
                'ward' => 'Respiratory & Isolation Unit',
                'room' => 'ISO-Neg 1',
                'bed' => 'Bed 01',
                'classification' => 'Inpatient',
                'respiratory' => 'High-Flow Mask (6 LPM)',
                'complaint' => 'Acute febrile viral respiratory infection with dense bilateral infiltrates. Severe coughing bouts and fatigue.',
                'vitals' => ['bp' => '105/68', 'temp' => '38.7', 'pulse' => '96', 'rr' => '24', 'spo2' => '94', 'weight' => '47'],
                'notes' => 'Strict negative-pressure airborne isolation. PPE required upon entry. Supplemental O2 titrated to SpO2 >= 95%.',
                'endorsement_status' => 'acknowledged',
                'hours_ago' => 20,
            ],
        ];

        $admissionModels = [];

        foreach ($admissionsConfig as $cfg) {
            $patient = $patientModels[$cfg['patient_key']] ?? null;
            $bed = $getBed($cfg['ward'], $cfg['room'], $cfg['bed']);

            if (! $patient || ! $bed) {
                continue;
            }

            $bed->update(['occupancy_status' => 'occupied']);

            $admission = Admission::updateOrCreate(
                [
                    'patient_id' => $patient->id,
                    'status' => 'admitted',
                ],
                [
                    'bed_id' => $bed->id,
                    'admitted_by' => $admissionStaff->id,
                    'patient_classification' => $cfg['classification'],
                    'respiratory_status' => $cfg['respiratory'],
                    'chief_complaint' => $cfg['complaint'],
                    'baseline_vitals' => $cfg['vitals'],
                    'admission_date' => Carbon::now()->subHours($cfg['hours_ago']),
                ]
            );

            $admissionModels[$cfg['patient_key']] = $admission;

            PatientEndorsement::updateOrCreate(
                ['admission_id' => $admission->id],
                [
                    'endorsed_by' => $admissionStaff->id,
                    'receiving_nurse_id' => $cfg['endorsement_status'] === 'acknowledged' ? $nurse->id : null,
                    'nurse_station' => $bed->ward->nurse_station_assigned,
                    'endorsement_notes' => $cfg['notes'],
                    'acknowledgment_status' => $cfg['endorsement_status'],
                    'acknowledged_at' => $cfg['endorsement_status'] === 'acknowledged' ? Carbon::now()->subHours(max(1, $cfg['hours_ago'] - 1)) : null,
                ]
            );
        }

        // 4. Inpatient Prescriptions, Dispensing & Billing Records
        $paracetamol = Medicine::where('code', 'PAR-500-TAB')->first();
        $amoxicillin = Medicine::where('code', 'AMX-500-CAP')->first();
        $salbutamol = Medicine::where('code', 'SLB-002-SYR')->first();
        $cetirizine = Medicine::where('code', 'CET-010-TAB')->first();
        $coAmox = Medicine::where('code', 'AMX-CLV-625')->first();

        // Prescription 1: Inpatient Fulfilled for Eduardo Mendoza (Med-Surg Room 101 Bed A)
        if ($eduardoAdmission = ($admissionModels['USM-2026-07777'] ?? null)) {
            $rx1 = Prescription::updateOrCreate(
                [
                    'admission_id' => $eduardoAdmission->id,
                    'patient_id' => $eduardoAdmission->patient_id,
                    'order_type' => 'inpatient',
                ],
                [
                    'encoded_by' => $nurse->id,
                    'doctor_name' => 'Dr. Maria Santos, MD',
                    'room_bed_number' => 'Room 101 - Bed A',
                    'status' => 'dispensed',
                    'billing_status' => 'billed_to_account',
                    'collected_by' => 'Nurse 001 (Station 2)',
                    'created_at' => Carbon::now()->subHours(14),
                    'updated_at' => Carbon::now()->subHours(12),
                ]
            );

            if ($amoxicillin) {
                PrescriptionItem::updateOrCreate(
                    ['prescription_id' => $rx1->id, 'medicine_id' => $amoxicillin->id],
                    ['quantity' => 21, 'dosage_instructions' => '500mg PO TID (every 8 hours) with meals x 7 days']
                );
            }
            if ($paracetamol) {
                PrescriptionItem::updateOrCreate(
                    ['prescription_id' => $rx1->id, 'medicine_id' => $paracetamol->id],
                    ['quantity' => 10, 'dosage_instructions' => '500mg PO Q4H PRN for temperature >= 38.0°C']
                );
            }

            // Create Inpatient POS Transaction & Patient Bill
            $batchAmx = StockBatch::where('medicine_id', $amoxicillin->id)->first();
            $batchPcm = StockBatch::where('medicine_id', $paracetamol->id)->first();

            $subtotal1 = (21 * 12.00) + (10 * 5.00); // 252 + 50 = 302
            $posTx1 = PosTransaction::updateOrCreate(
                ['prescription_id' => $rx1->id],
                [
                    'cashier_id' => $pharmacist->id,
                    'order_type' => 'inpatient',
                    'room_bed_number' => 'Room 101 - Bed A',
                    'billing_status' => 'billed_to_account',
                    'collected_by' => 'Nurse 001 (Station 2)',
                    'subtotal' => $subtotal1,
                    'total_amount' => $subtotal1,
                    'discount_type' => 'regular',
                    'discount_amount' => 0.00,
                    'net_amount' => $subtotal1,
                    'payment_method' => 'account',
                    'created_at' => Carbon::now()->subHours(12),
                ]
            );

            if ($batchAmx) {
                PosTransactionItem::updateOrCreate(
                    ['pos_transaction_id' => $posTx1->id, 'medicine_id' => $amoxicillin->id],
                    ['batch_id' => $batchAmx->id, 'quantity' => 21, 'unit_price' => 12.00, 'subtotal' => 252.00]
                );
            }
            if ($batchPcm) {
                PosTransactionItem::updateOrCreate(
                    ['pos_transaction_id' => $posTx1->id, 'medicine_id' => $paracetamol->id],
                    ['batch_id' => $batchPcm->id, 'quantity' => 10, 'unit_price' => 5.00, 'subtotal' => 50.00]
                );
            }

            PatientBill::updateOrCreate(
                ['prescription_id' => $rx1->id],
                [
                    'patient_id' => $eduardoAdmission->patient_id,
                    'pos_transaction_id' => $posTx1->id,
                    'room_bed_number' => 'Room 101 - Bed A',
                    'doctor_name' => 'Dr. Maria Santos, MD',
                    'gross_amount' => $subtotal1,
                    'discount_amount' => 0.00,
                    'net_amount' => $subtotal1,
                    'status' => 'billed_to_account',
                    'billed_by' => $pharmacist->id,
                    'created_at' => Carbon::now()->subHours(12),
                ]
            );
        }

        // Prescription 2: Inpatient ROUTED (Waiting in Pharmacy queue!) for Juan Dela Cruz (ER)
        if ($juanAdmission = ($admissionModels['USM-2024-00142'] ?? null)) {
            $rx2 = Prescription::updateOrCreate(
                [
                    'admission_id' => $juanAdmission->id,
                    'patient_id' => $juanAdmission->patient_id,
                    'order_type' => 'inpatient',
                ],
                [
                    'encoded_by' => $nurse->id,
                    'doctor_name' => 'Dr. Ramon Reyes, FPCP',
                    'room_bed_number' => 'ER-Bay 1 - Bed 01',
                    'status' => 'routed',
                    'billing_status' => 'pending_payment',
                    'created_at' => Carbon::now()->subMinutes(45),
                    'updated_at' => Carbon::now()->subMinutes(45),
                ]
            );

            if ($salbutamol) {
                PrescriptionItem::updateOrCreate(
                    ['prescription_id' => $rx2->id, 'medicine_id' => $salbutamol->id],
                    ['quantity' => 1, 'dosage_instructions' => '5ml PO Q6H for 5 days']
                );
            }
            if ($paracetamol) {
                PrescriptionItem::updateOrCreate(
                    ['prescription_id' => $rx2->id, 'medicine_id' => $paracetamol->id],
                    ['quantity' => 6, 'dosage_instructions' => '500mg PO Q4H PRN for fever']
                );
            }
        }

        // Prescription 3: Inpatient ROUTED for Princess Althea (Isolation Ward)
        if ($altheaAdmission = ($admissionModels['USM-2024-03211'] ?? null)) {
            $rx3 = Prescription::updateOrCreate(
                [
                    'admission_id' => $altheaAdmission->id,
                    'patient_id' => $altheaAdmission->patient_id,
                    'order_type' => 'inpatient',
                ],
                [
                    'encoded_by' => $nurse->id,
                    'doctor_name' => 'Dr. Liza Tan, MD',
                    'room_bed_number' => 'ISO-Neg 1 - Bed 01',
                    'status' => 'routed',
                    'billing_status' => 'pending_payment',
                    'created_at' => Carbon::now()->subHours(3),
                    'updated_at' => Carbon::now()->subHours(3),
                ]
            );

            if ($cetirizine) {
                PrescriptionItem::updateOrCreate(
                    ['prescription_id' => $rx3->id, 'medicine_id' => $cetirizine->id],
                    ['quantity' => 14, 'dosage_instructions' => '10mg PO once daily at bedtime x 14 days']
                );
            }
            if ($paracetamol) {
                PrescriptionItem::updateOrCreate(
                    ['prescription_id' => $rx3->id, 'medicine_id' => $paracetamol->id],
                    ['quantity' => 12, 'dosage_instructions' => '500mg PO Q4H PRN for fever >= 38.0°C']
                );
            }
        }

        // 5. Over-The-Counter (OTC) POS Transactions with Discounts
        // Sale A: Senior Citizen OTC Sale
        $attyQuezon = $patientModels['RES-2026-0155'] ?? null;
        $batchPcm = StockBatch::where('medicine_id', $paracetamol?->id)->first();
        $batchCet = StockBatch::where('medicine_id', $cetirizine?->id)->first();

        if ($attyQuezon && $batchPcm && $batchCet) {
            $subtotalOTC1 = (20 * 5.00) + (10 * 11.00); // 100 + 110 = 210
            $discount1 = round($subtotalOTC1 * 0.20, 2); // 42.00 Senior Citizen 20%
            $net1 = $subtotalOTC1 - $discount1; // 168.00

            $posOTC1 = PosTransaction::updateOrCreate(
                ['discount_id_number' => 'OSCA-2020-0442'],
                [
                    'cashier_id' => $pharmacist->id,
                    'order_type' => 'outpatient',
                    'billing_status' => 'paid',
                    'subtotal' => $subtotalOTC1,
                    'total_amount' => $subtotalOTC1,
                    'discount_type' => 'senior_citizen',
                    'discount_id_number' => 'OSCA-2020-0442',
                    'vat_exempt_amount' => $subtotalOTC1,
                    'discount_amount' => $discount1,
                    'net_amount' => $net1,
                    'payment_method' => 'cash',
                    'created_at' => Carbon::now()->subHours(5),
                ]
            );

            PosTransactionItem::updateOrCreate(
                ['pos_transaction_id' => $posOTC1->id, 'medicine_id' => $paracetamol->id],
                ['batch_id' => $batchPcm->id, 'quantity' => 20, 'unit_price' => 5.00, 'subtotal' => 100.00]
            );
            PosTransactionItem::updateOrCreate(
                ['pos_transaction_id' => $posOTC1->id, 'medicine_id' => $cetirizine->id],
                ['batch_id' => $batchCet->id, 'quantity' => 10, 'unit_price' => 11.00, 'subtotal' => 110.00]
            );
        }

        // Sale B: Student OTC Sale (Paracetamol)
        if ($batchPcm) {
            $subtotalOTC2 = 10 * 5.00; // 50.00
            $discount2 = round($subtotalOTC2 * 0.10, 2); // 5.00 student discount
            $net2 = $subtotalOTC2 - $discount2; // 45.00

            $posOTC2 = PosTransaction::updateOrCreate(
                ['discount_id_number' => 'USM-STUDENT-2026-90'],
                [
                    'cashier_id' => $pharmacist->id,
                    'order_type' => 'outpatient',
                    'billing_status' => 'paid',
                    'subtotal' => $subtotalOTC2,
                    'total_amount' => $subtotalOTC2,
                    'discount_type' => 'student',
                    'discount_id_number' => 'USM-STUDENT-2026-90',
                    'vat_exempt_amount' => 0.00,
                    'discount_amount' => $discount2,
                    'net_amount' => $net2,
                    'payment_method' => 'cash',
                    'created_at' => Carbon::now()->subHours(1),
                ]
            );

            PosTransactionItem::updateOrCreate(
                ['pos_transaction_id' => $posOTC2->id, 'medicine_id' => $paracetamol->id],
                ['batch_id' => $batchPcm->id, 'quantity' => 10, 'unit_price' => 5.00, 'subtotal' => 50.00]
            );
        }

        // 6. Warehouse Restock Deliveries (Movements Hub)
        // Delivery 1: Pending Restock Order triggered by Decision Support Hub
        $aziMed = Medicine::where('code', 'AZI-500-TAB')->first();
        if ($aziMed) {
            $del1 = Delivery::updateOrCreate(
                ['reference_no' => 'REQ-2026-0041'],
                [
                    'supplier' => 'Unilab Corp.',
                    'delivery_date' => Carbon::now()->addDays(7)->toDateString(),
                    'status' => 'pending',
                    'notes' => 'Algorithmic reorder recommendation triggered from Dual-Risk Hub. Azithromycin 500mg deficit restock.',
                    'created_by' => $stockManager->id,
                    'created_at' => Carbon::now()->subHours(2),
                ]
            );

            DeliveryItem::updateOrCreate(
                ['delivery_id' => $del1->id, 'medicine_id' => $aziMed->id],
                ['quantity' => 140, 'batch_no' => 'PEND-AZI-2026B', 'expiry_date' => Carbon::now()->addMonths(18)->toDateString()]
            );
        }

        // Delivery 2: Delivered / Received Inbound Shipment
        if ($paracetamol) {
            $del2 = Delivery::updateOrCreate(
                ['reference_no' => 'DEL-2026-0812'],
                [
                    'supplier' => 'Unilab Corp.',
                    'delivery_date' => Carbon::now()->subDays(5)->toDateString(),
                    'status' => 'delivered',
                    'notes' => 'Emergency replenishing stock shipment received and inspected under standard QC.',
                    'created_by' => $stockManager->id,
                    'received_by' => $stockManager->id,
                    'confirmed_at' => Carbon::now()->subDays(5),
                    'created_at' => Carbon::now()->subDays(6),
                ]
            );

            DeliveryItem::updateOrCreate(
                ['delivery_id' => $del2->id, 'medicine_id' => $paracetamol->id],
                ['quantity' => 500, 'batch_no' => 'BATCH-PCM-2026B', 'expiry_date' => Carbon::now()->addMonths(18)->toDateString()]
            );
        }

        // Delivery 3: Delivered from GSK
        if ($salbutamol) {
            $del3 = Delivery::updateOrCreate(
                ['reference_no' => 'DEL-2026-0740'],
                [
                    'supplier' => 'GSK Philippines',
                    'delivery_date' => Carbon::now()->subWeeks(2)->toDateString(),
                    'status' => 'delivered',
                    'notes' => 'Regular respiratory stock batch intake.',
                    'created_by' => $stockManager->id,
                    'received_by' => $stockManager->id,
                    'confirmed_at' => Carbon::now()->subWeeks(2),
                    'created_at' => Carbon::now()->subWeeks(2),
                ]
            );

            DeliveryItem::updateOrCreate(
                ['delivery_id' => $del3->id, 'medicine_id' => $salbutamol->id],
                ['quantity' => 50, 'batch_no' => 'BATCH-SLB-2026A', 'expiry_date' => Carbon::now()->addMonths(9)->toDateString()]
            );
        }

        // Delivery 4: Cancelled Shipment
        if ($amoxicillin) {
            Delivery::updateOrCreate(
                ['reference_no' => 'PO-2026-0504'],
                [
                    'supplier' => 'Rhea Generics',
                    'delivery_date' => Carbon::now()->subWeeks(3)->toDateString(),
                    'status' => 'cancelled',
                    'notes' => 'Duplicate procurement order cancelled upon stock review.',
                    'created_by' => $stockManager->id,
                    'created_at' => Carbon::now()->subWeeks(3),
                ]
            );
        }

        // 7. Realistic System Audit Trail Logs
        $auditEvents = [
            [
                'user_id' => $admin->id,
                'action' => 'LOGIN',
                'module' => 'Authentication',
                'description' => 'Administrator successfully authenticated into the USM Hospital clinical workspace.',
                'ip' => '127.0.0.1',
                'ago' => 20,
            ],
            [
                'user_id' => $admissionStaff->id,
                'action' => 'REGISTER_PATIENT',
                'module' => 'Admission',
                'description' => 'Registered new patient intake for Juan Dela Cruz [USM-2024-00142] (Category: USM Student).',
                'ip' => '127.0.0.1',
                'ago' => 18,
            ],
            [
                'user_id' => $admissionStaff->id,
                'action' => 'ASSIGN_BED',
                'module' => 'Ward Management',
                'description' => 'Assigned Juan Dela Cruz to Emergency & Trauma Complex -> ER-Bay 1, Bed 01.',
                'ip' => '127.0.0.1',
                'ago' => 17,
            ],
            [
                'user_id' => $admissionStaff->id,
                'action' => 'ENDORSE_PATIENT',
                'module' => 'Clinical Endorsement',
                'description' => 'Dispatched clinical endorsement for Juan Dela Cruz to Station 1 - Emergency & Triage.',
                'ip' => '127.0.0.1',
                'ago' => 17,
            ],
            [
                'user_id' => $nurse->id,
                'action' => 'ACKNOWLEDGE_ENDORSEMENT',
                'module' => 'Nurse Station',
                'description' => 'Nurse acknowledged endorsement for Eduardo Mendoza at Central Inpatient Wing.',
                'ip' => '127.0.0.1',
                'ago' => 15,
            ],
            [
                'user_id' => $nurse->id,
                'action' => 'ROUTE_PRESCRIPTION',
                'module' => 'Prescriptions',
                'description' => 'Encoded and routed inpatient prescription for Eduardo Mendoza (Amoxicillin 500mg, Paracetamol 500mg).',
                'ip' => '127.0.0.1',
                'ago' => 14,
            ],
            [
                'user_id' => $pharmacist->id,
                'action' => 'DISPENSE_INPATIENT_PRESCRIPTION',
                'module' => 'Pharmacy POS',
                'description' => 'Dispensed inpatient prescription for Eduardo Mendoza (Room 101 Bed A). Collected by: Nurse 001.',
                'ip' => '127.0.0.1',
                'ago' => 12,
            ],
            [
                'user_id' => $pharmacist->id,
                'action' => 'OTC_SALE',
                'module' => 'Pharmacy POS',
                'description' => 'Completed OTC transaction for Senior Citizen Atty. Manuel Quezon (OSCA-2020-0442). Total: ₱168.00.',
                'ip' => '127.0.0.1',
                'ago' => 5,
            ],
            [
                'user_id' => $stockManager->id,
                'action' => 'RESTOCK_REQUEST_CREATED',
                'module' => 'Warehouse Inventory',
                'description' => 'Generated restock requisition REQ-2026-0041 for Azithromycin 500mg (140 units) based on Dual-Risk alert.',
                'ip' => '127.0.0.1',
                'ago' => 2,
            ],
            [
                'user_id' => $stockManager->id,
                'action' => 'RECALCULATE_RISK_ENGINE',
                'module' => 'Predictive Analytics',
                'description' => 'Triggered dual-engine algorithmic recalculation across 9 active formulations and 12 batches.',
                'ip' => '127.0.0.1',
                'ago' => 1,
            ],
        ];

        foreach ($auditEvents as $ev) {
            AuditLog::create([
                'user_id' => $ev['user_id'],
                'action' => $ev['action'],
                'module' => $ev['module'],
                'description' => $ev['description'],
                'ip_address' => $ev['ip'],
                'created_at' => Carbon::now()->subHours($ev['ago']),
                'updated_at' => Carbon::now()->subHours($ev['ago']),
            ]);
        }
    }
}
