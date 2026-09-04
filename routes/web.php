<?php

use App\Http\Controllers\PosController;
use App\Http\Controllers\PrescriptionController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Prescription Module (nurse, medical_secretary, admin)
Route::middleware(['auth', 'role:nurse|medical_secretary'])->prefix('prescriptions')->name('prescriptions.')->group(function () {
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
