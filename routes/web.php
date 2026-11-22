<?php

use App\Http\Controllers\Admin\AdminAuditController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminReportController;
use App\Http\Controllers\Admin\AdminSettingController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AdminWardController;
use App\Http\Controllers\AdmissionController;
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\NurseStationController;
use App\Http\Controllers\PatientPortalController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\PrescriptionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WelcomeController;
use App\Models\Admission;
use App\Models\Medicine;
use App\Models\PatientEndorsement;
use App\Models\PosTransaction;
use App\Models\Prescription;
use App\Models\StockBatch;
use App\Models\StockMovement;
use App\Models\Ward;
use App\Services\RiskPredictionService;
use Illuminate\Support\Facades\Route;

Route::get('/', [WelcomeController::class, 'index'])->name('home');
Route::get('/medicines', [WelcomeController::class, 'medicines'])->name('medicines');
Route::post('/chatbot/message', [ChatbotController::class, 'message'])->name('chatbot.message');

Route::get('/dashboard', function () {
    if (auth()->user()->hasRole('patient')) {
        return redirect()->route('patient.dashboard');
    }
    if (auth()->user()->hasRole('admission')) {
        return redirect()->route('admission.index');
    }

    $counts = [
        'all' => Prescription::count(),
        'pending' => Prescription::pending()->count(),
        'routed' => Prescription::routed()->count(),
        'dispensed' => Prescription::dispensed()->count(),
        'cancelled' => Prescription::cancelled()->count(),
    ];

    $recentPrescriptions = Prescription::with(['patient.user', 'encodedBy', 'items.medicine'])
        ->latest()
        ->take(5)
        ->get();

    $allMedicines = Medicine::with(['stockBatches' => function ($q) {
        $q->where('status', 'received')
            ->where('expiry_date', '>=', now()->toDateString())
            ->where('quantity_remaining', '>', 0);
    }])->get();

    $inventoryCounts = [
        'total' => $allMedicines->count(),
        'in_stock' => $allMedicines->filter(fn ($m) => $m->available_stock > $m->reorder_level)->count(),
        'low_stock' => $allMedicines->filter(fn ($m) => $m->available_stock > 0 && $m->available_stock <= $m->reorder_level)->count(),
        'out_of_stock' => $allMedicines->filter(fn ($m) => $m->available_stock === 0)->count(),
        'expiring_soon' => StockBatch::expiringSoon(30)->count(),
    ];

    $recentMovements = StockMovement::with(['medicine', 'batch', 'createdBy'])->latest()->take(6)->get();

    $inventory = $allMedicines->take(8);

    $riskEngine = app(RiskPredictionService::class)->getDualEngineInsights(7);

    $pendingEndorsements = PatientEndorsement::with(['admission.patient.user', 'admission.bed.ward', 'endorsedBy'])
        ->where('acknowledgment_status', 'pending')
        ->latest()
        ->get();

    $activeInpatients = Admission::with(['patient.user', 'bed.ward', 'prescriptions'])
        ->where('status', 'admitted')
        ->latest('admission_date')
        ->get();

    $wards = Ward::with(['beds'])->where('status', 'active')->get();

    $posTodaySales = (float) PosTransaction::whereDate('created_at', now()->toDateString())->sum('total_amount');
    $posTodayCount = PosTransaction::whereDate('created_at', now()->toDateString())->count();

    return view('dashboard', compact(
        'counts',
        'recentPrescriptions',
        'inventory',
        'inventoryCounts',
        'recentMovements',
        'riskEngine',
        'pendingEndorsements',
        'activeInpatients',
        'wards',
        'posTodaySales',
        'posTodayCount'
    ));
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Patient Portal (patients only)
Route::middleware(['auth', 'role:patient'])->prefix('patient')->name('patient.')->group(function () {
    Route::get('/dashboard', [PatientPortalController::class, 'dashboard'])->name('dashboard');
    Route::get('/prescriptions', [PatientPortalController::class, 'prescriptions'])->name('prescriptions');
    Route::get('/prescriptions/{prescription}', [PatientPortalController::class, 'showPrescription'])->name('prescriptions.show');
    Route::get('/profile', [PatientPortalController::class, 'profile'])->name('profile');
    Route::patch('/profile', [PatientPortalController::class, 'updateProfile'])->name('profile.update');
});

// Admission Module (admission, admin)
Route::middleware(['auth', 'role:admission'])->prefix('admission')->name('admission.')->group(function () {
    Route::get('/', [AdmissionController::class, 'index'])->name('index');
    Route::get('/beds', [AdmissionController::class, 'beds'])->name('beds');
    Route::get('/patients', [AdmissionController::class, 'patients'])->name('patients');
    Route::post('/patients', [AdmissionController::class, 'storePatient'])->name('patients.store');
    Route::get('/admit/{patient}', [AdmissionController::class, 'createAdmission'])->name('create');
    Route::post('/admit', [AdmissionController::class, 'storeAdmission'])->name('store');
    Route::post('/admit/{admission}/discharge', [AdmissionController::class, 'discharge'])->name('discharge');
});

// Nurse Station Module (nurse, admin)
Route::middleware(['auth', 'role:nurse'])->prefix('nurse')->name('nurse.')->group(function () {
    Route::get('/dashboard', [NurseStationController::class, 'dashboard'])->name('dashboard');
    Route::get('/endorsements', [NurseStationController::class, 'endorsements'])->name('endorsements');
    Route::post('/endorsements/{endorsement}/acknowledge', [NurseStationController::class, 'acknowledgeEndorsement'])->name('endorsements.acknowledge');
    Route::get('/inpatients', [NurseStationController::class, 'inpatients'])->name('inpatients');
    Route::get('/chart/{admission}', [NurseStationController::class, 'showChart'])->name('chart');
    Route::get('/chart/{admission}/medication-summary', [NurseStationController::class, 'printMedicationSummary'])->name('chart.summary');
    Route::post('/chart/{admission}/vitals', [NurseStationController::class, 'updateVitals'])->name('chart.vitals');
    Route::post('/chart/{admission}/transfer', [NurseStationController::class, 'transferBed'])->name('admissions.transfer');
    Route::post('/chart/{admission}/discharge', [NurseStationController::class, 'discharge'])->name('admissions.discharge');
});

// Prescription Module (nurse, admin)
Route::middleware(['auth', 'role:nurse'])->prefix('prescriptions')->name('prescriptions.')->group(function () {
    Route::get('/', [PrescriptionController::class, 'index'])->name('index');
    Route::get('/inventory', [PrescriptionController::class, 'inventory'])->name('inventory');
    Route::get('/patients', [PrescriptionController::class, 'patients'])->name('patients');
    Route::get('/create', [PrescriptionController::class, 'create'])->name('create');
    Route::post('/', [PrescriptionController::class, 'store'])->name('store');
    Route::get('/{prescription}', [PrescriptionController::class, 'show'])->name('show');
    Route::post('/{prescription}/route', [PrescriptionController::class, 'routeToPharmacy'])->name('route');
    Route::delete('/{prescription}', [PrescriptionController::class, 'cancel'])->name('cancel');
});

// Pharmacy/POS Module (pharmacist, admin)
Route::middleware(['auth', 'role:pharmacist'])->prefix('pos')->name('pos.')->group(function () {
    Route::get('/', [PosController::class, 'index'])->name('index');
    Route::get('/reports', [PosController::class, 'reports'])->name('reports');
    Route::get('/otc', [PosController::class, 'otcCreate'])->name('otc.create');
    Route::post('/otc', [PosController::class, 'otcStore'])->name('otc.store');
    Route::get('/receipt/{transaction}', [PosController::class, 'receipt'])->name('receipt');
    Route::post('/prescriptions/{prescription}/prepare', [PosController::class, 'markPrepared'])->name('prepare');
    Route::get('/{prescription}', [PosController::class, 'process'])->name('process');
    Route::post('/{prescription}', [PosController::class, 'dispense'])->name('dispense');
});

// Dual-Risk Prediction Engine Hub (stock_manager, pharmacist, admin - read-only analytics for pharmacists)
Route::middleware(['auth', 'role:stock_manager|pharmacist'])->prefix('inventory')->name('inventory.')
    ->group(function () {
        Route::get('/risk-engine', [InventoryController::class, 'riskEngineHub'])->name('risk-engine');
    });

// Inventory Module (stock_manager, admin)
Route::middleware(['auth', 'role:stock_manager'])->prefix('inventory')->name('inventory.')
    ->group(function () {
        Route::get('/', [InventoryController::class, 'index'])->name('index');
        Route::post('/risk-engine/recalculate', [InventoryController::class, 'recalculateRiskEngine'])->name('risk-engine.recalculate');
        Route::get('/export-pdf', [InventoryController::class, 'exportPdf'])->name('export-pdf');
        Route::get('/movements', [InventoryController::class, 'movements'])->name('movements');

        // Multi-medicine deliveries
        Route::get('/deliveries/create', [InventoryController::class, 'createDelivery'])->name('deliveries.create');
        Route::post('/deliveries', [InventoryController::class, 'storeDelivery'])->name('deliveries.store');
        Route::post('/deliveries/{delivery}/confirm', [InventoryController::class, 'confirmDeliveryRequest'])->name('deliveries.confirm');
        Route::post('/deliveries/{delivery}/cancel', [InventoryController::class, 'cancelDeliveryRequest'])->name('deliveries.cancel');

        Route::get('/medicines/create', [InventoryController::class, 'create'])->name('medicines.create');
        Route::post('/medicines', [InventoryController::class, 'store'])->name('medicines.store');
        Route::get('/medicines/{medicine}', [InventoryController::class, 'show'])->name('medicines.show');
        Route::get('/medicines/{medicine}/edit', [InventoryController::class, 'edit'])->name('medicines.edit');
        Route::patch('/medicines/{medicine}', [InventoryController::class, 'update'])->name('medicines.update');
        Route::get('/medicines/{medicine}/receive', [InventoryController::class, 'receiveBatch'])->name('medicines.receive');
        Route::post('/medicines/{medicine}/receive', [InventoryController::class, 'storeReceivedBatch'])->name('medicines.receive.store');
        Route::post('/batches/{batch}/confirm', [InventoryController::class, 'confirmDelivery'])->name('batches.confirm');
        Route::post('/batches/{batch}/reject', [InventoryController::class, 'rejectDelivery'])->name('batches.reject');
        Route::post('/batches/{batch}/dispose', [InventoryController::class, 'disposeBatch'])->name('batches.dispose');
        Route::post('/batches/{batch}/adjust', [InventoryController::class, 'adjustStock'])->name('batches.adjust');
    });

// Admin Portal Module (admin only)
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
    Route::post('/users', [AdminUserController::class, 'store'])->name('users.store');
    Route::put('/users/{user}', [AdminUserController::class, 'update'])->name('users.update');
    Route::post('/users/{user}/toggle-status', [AdminUserController::class, 'toggleStatus'])->name('users.toggle-status');
    Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');
    Route::get('/audit-logs', [AdminAuditController::class, 'index'])->name('audit-logs');
    Route::get('/wards', [AdminWardController::class, 'index'])->name('wards.index');
    Route::post('/wards', [AdminWardController::class, 'storeWard'])->name('wards.store');
    Route::put('/wards/{ward}', [AdminWardController::class, 'updateWard'])->name('wards.update');
    Route::post('/wards/{ward}/beds', [AdminWardController::class, 'storeBed'])->name('wards.beds.store');
    Route::post('/beds/{bed}/toggle-status', [AdminWardController::class, 'toggleBedStatus'])->name('beds.toggle-status');
    Route::get('/reports', [AdminReportController::class, 'index'])->name('reports');
    Route::get('/settings', [AdminSettingController::class, 'index'])->name('settings.index');
    Route::post('/settings', [AdminSettingController::class, 'update'])->name('settings.update');
});

require __DIR__.'/auth.php';
