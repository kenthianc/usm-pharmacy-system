<?php

namespace Database\Seeders;

use App\Models\Medicine;
use App\Models\StockBatch;
use App\Models\StockMovement;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class MedicineAndBatchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Cleanup demo naming if already in database
        Medicine::where('name', 'Azithromycin 500mg (Out of Stock Demo)')
            ->update(['name' => 'Azithromycin 500mg', 'code' => 'AZI-500-TAB', 'barcode' => '4800016644081']);

        $catalog = [
            [
                'code' => 'PAR-500-TAB',
                'barcode' => '4800016644012',
                'name' => 'Paracetamol 500mg',
                'generic_name' => 'Paracetamol',
                'category' => 'Analgesic / Antipyretic',
                'unit' => 'tablet',
                'unit_price' => 5.00,
                'reorder_level' => 50,
                'batches' => [
                    [
                        'batch_no' => 'BATCH-PCM-2026A',
                        'quantity_received' => 500,
                        'quantity_remaining' => 250,
                        'expiry_date' => Carbon::now()->addMonths(8)->toDateString(),
                        'received_date' => Carbon::now()->subMonths(1)->toDateString(),
                        'supplier' => 'Unilab Corp.',
                    ],
                    [
                        'batch_no' => 'BATCH-PCM-2026B',
                        'quantity_received' => 500,
                        'quantity_remaining' => 500,
                        'expiry_date' => Carbon::now()->addMonths(18)->toDateString(),
                        'received_date' => Carbon::now()->subDays(10)->toDateString(),
                        'supplier' => 'Unilab Corp.',
                    ],
                ],
            ],
            [
                'code' => 'AMX-500-CAP',
                'barcode' => '4800016644029',
                'name' => 'Amoxicillin 500mg',
                'generic_name' => 'Amoxicillin Trihydrate',
                'category' => 'Antibiotic',
                'unit' => 'capsule',
                'unit_price' => 12.00,
                'reorder_level' => 30,
                'batches' => [
                    [
                        'batch_no' => 'BATCH-AMX-2026A',
                        'quantity_received' => 300,
                        'quantity_remaining' => 180,
                        'expiry_date' => Carbon::now()->addMonths(6)->toDateString(),
                        'received_date' => Carbon::now()->subMonths(2)->toDateString(),
                        'supplier' => 'Rhea Generics',
                    ],
                ],
            ],
            [
                'code' => 'MEF-500-CAP',
                'barcode' => '4800016644036',
                'name' => 'Mefenamic Acid 500mg',
                'generic_name' => 'Mefenamic Acid',
                'category' => 'NSAID / Pain Relief',
                'unit' => 'capsule',
                'unit_price' => 8.50,
                'reorder_level' => 30,
                'batches' => [
                    [
                        'batch_no' => 'BATCH-MEF-2026A',
                        'quantity_received' => 250,
                        'quantity_remaining' => 190,
                        'expiry_date' => Carbon::now()->addMonths(12)->toDateString(),
                        'received_date' => Carbon::now()->subWeeks(3)->toDateString(),
                        'supplier' => 'RiteMed',
                    ],
                ],
            ],
            [
                'code' => 'CET-010-TAB',
                'barcode' => '4800016644043',
                'name' => 'Cetirizine 10mg',
                'generic_name' => 'Cetirizine Dihydrochloride',
                'category' => 'Antihistamine',
                'unit' => 'tablet',
                'unit_price' => 6.00,
                'reorder_level' => 40,
                'batches' => [
                    [
                        'batch_no' => 'BATCH-CET-2026A',
                        'quantity_received' => 200,
                        'quantity_remaining' => 140,
                        'expiry_date' => Carbon::now()->addMonths(14)->toDateString(),
                        'received_date' => Carbon::now()->subMonth()->toDateString(),
                        'supplier' => 'RiteMed',
                    ],
                ],
            ],
            [
                'code' => 'OMP-020-CAP',
                'barcode' => '4800016644050',
                'name' => 'Omeprazole 20mg',
                'generic_name' => 'Omeprazole',
                'category' => 'Antacid / PPI',
                'unit' => 'capsule',
                'unit_price' => 15.00,
                'reorder_level' => 25,
                'batches' => [
                    [
                        'batch_no' => 'BATCH-OMP-2026A',
                        'quantity_received' => 150,
                        'quantity_remaining' => 85,
                        'expiry_date' => Carbon::now()->addMonths(10)->toDateString(),
                        'received_date' => Carbon::now()->subMonths(2)->toDateString(),
                        'supplier' => 'Pascual Laboratories',
                    ],
                ],
            ],
            [
                'code' => 'SLB-002-SYR',
                'barcode' => '4800016644067',
                'name' => 'Salbutamol 2mg/5ml Syrup 60ml',
                'generic_name' => 'Salbutamol Sulfate',
                'category' => 'Bronchodilator',
                'unit' => 'bottle',
                'unit_price' => 85.00,
                'reorder_level' => 15,
                'batches' => [
                    [
                        'batch_no' => 'BATCH-SLB-2026A',
                        'quantity_received' => 50,
                        'quantity_remaining' => 22,
                        'expiry_date' => Carbon::now()->addMonths(9)->toDateString(),
                        'received_date' => Carbon::now()->subMonth()->toDateString(),
                        'supplier' => 'GSK Philippines',
                    ],
                    [
                        'batch_no' => 'BATCH-SLB-2026EXP',
                        'quantity_received' => 30,
                        'quantity_remaining' => 18,
                        'expiry_date' => Carbon::now()->addDays(14)->toDateString(),
                        'received_date' => Carbon::now()->subMonths(11)->toDateString(),
                        'supplier' => 'GSK Philippines',
                    ],
                ],
            ],
            [
                'code' => 'ORS-000-SAC',
                'barcode' => '4800016644074',
                'name' => 'Oral Rehydration Salts',
                'generic_name' => 'Sodium Chloride + Potassium Chloride + Sodium Citrate + Glucose',
                'category' => 'Electrolytes',
                'unit' => 'sachet',
                'unit_price' => 10.00,
                'reorder_level' => 50,
                'batches' => [
                    [
                        'batch_no' => 'BATCH-ORS-2026A',
                        'quantity_received' => 400,
                        'quantity_remaining' => 310,
                        'expiry_date' => Carbon::now()->addMonths(24)->toDateString(),
                        'received_date' => Carbon::now()->subWeeks(2)->toDateString(),
                        'supplier' => 'Abbott Nutrition',
                    ],
                ],
            ],
            [
                'code' => 'AZI-500-TAB',
                'barcode' => '4800016644081',
                'name' => 'Azithromycin 500mg',
                'generic_name' => 'Azithromycin',
                'category' => 'Antibiotic',
                'unit' => 'tablet',
                'unit_price' => 45.00,
                'reorder_level' => 20,
                'batches' => [
                    [
                        'batch_no' => 'BATCH-AZI-2026A',
                        'quantity_received' => 50,
                        'quantity_remaining' => 0,
                        'expiry_date' => Carbon::now()->addMonths(5)->toDateString(),
                        'received_date' => Carbon::now()->subMonths(3)->toDateString(),
                        'supplier' => 'Unilab Corp.',
                    ],
                ],
            ],
        ];

        $stockUserId = User::whereHas('roles', fn ($q) => $q->whereIn('name', ['stock_manager', 'admin', 'pharmacist']))->value('id')
            ?? User::value('id')
            ?? 1;

        foreach ($catalog as $item) {
            $batches = $item['batches'];
            unset($item['batches']);

            $medicine = Medicine::updateOrCreate(
                ['name' => $item['name']],
                $item
            );

            foreach ($batches as $batch) {
                $batchModel = StockBatch::updateOrCreate(
                    [
                        'medicine_id' => $medicine->id,
                        'batch_no' => $batch['batch_no'],
                    ],
                    $batch
                );

                // Seed realistic stock movements if units were dispensed
                $dispensed = $batchModel->quantity_received - $batchModel->quantity_remaining;
                if ($dispensed > 0 && StockMovement::where('batch_id', $batchModel->id)->where('type', 'out')->count() === 0) {
                    $chunks = 4;
                    $chunkQty = (int) floor($dispensed / $chunks);
                    $remainder = $dispensed % $chunks;

                    for ($i = 0; $i < $chunks; $i++) {
                        $qty = $chunkQty + ($i === $chunks - 1 ? $remainder : 0);
                        if ($qty <= 0) {
                            continue;
                        }

                        $daysAgo = ($chunks - $i) * 5;
                        StockMovement::create([
                            'medicine_id' => $medicine->id,
                            'batch_id' => $batchModel->id,
                            'type' => 'out',
                            'quantity' => $qty,
                            'reference_type' => 'Prescription Dispense',
                            'created_by' => $stockUserId,
                            'created_at' => Carbon::now()->subDays($daysAgo),
                            'updated_at' => Carbon::now()->subDays($daysAgo),
                        ]);
                    }
                }
            }
        }
    }
}
