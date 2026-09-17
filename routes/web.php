<?php

use App\Http\Controllers\PatientPortalController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\PrescriptionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WelcomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', [WelcomeController::class, 'index'])->name('home');
Route::get('/medicines', [WelcomeController::class, 'medicines'])->name('medicines');

Route::get('/dashboard', function () {
    if (auth()->user()->hasRole('patient')) {
        return redirect()->route('patient.dashboard');
    }

    return view('dashboard');
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

// Prescription Module (nurse, admin)
Route::middleware(['auth', 'role:nurse'])->prefix('prescriptions')->name('prescriptions.')->group(function () {
    Route::get('/', [PrescriptionController::class, 'index'])->name('index');
    Route::get('/create', [PrescriptionController::class, 'create'])->name('create');
    Route::post('/', [PrescriptionController::class, 'store'])->name('store');
    Route::get('/{prescription}', [PrescriptionController::class, 'show'])->name('show');
    Route::post('/{prescription}/route', [PrescriptionController::class, 'routeToPharmacy'])->name('route');
    Route::delete('/{prescription}', [PrescriptionController::class, 'cancel'])->name('cancel');
});

// Pharmacy/POS Module (pharmacist, admin)
Route::middleware(['auth', 'role:pharmacist'])->prefix('pos')->name('pos.')->group(function () {
    Route::get('/', [PosController::class, 'index'])->name('index');
    Route::get('/otc', [PosController::class, 'otcCreate'])->name('otc.create');
    Route::post('/otc', [PosController::class, 'otcStore'])->name('otc.store');
    Route::get('/receipt/{transaction}', [PosController::class, 'receipt'])->name('receipt');
    Route::get('/{prescription}', [PosController::class, 'process'])->name('process');
    Route::post('/{prescription}', [PosController::class, 'dispense'])->name('dispense');
});

require __DIR__.'/auth.php';
