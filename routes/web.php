<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\EncounterController;
use App\Http\Controllers\MedicineController;
use App\Http\Controllers\AIPredictionController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
});

Route::get('/dashboard', function () {
    return view('dashboard-content');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Admin routes - Users, Roles, Medicines management
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::resource('users', UserController::class);
    Route::resource('roles', RoleController::class);
    Route::resource('medicines', MedicineController::class);

    // Additional medicine routes
    Route::post('medicines/{medicine}/update-stock', [MedicineController::class, 'updateStock'])->name('medicines.update-stock');
    Route::post('medicines/bulk-update-stock', [MedicineController::class, 'bulkUpdateStock'])->name('medicines.bulk-update-stock');
    Route::get('pharmacy/dashboard', [MedicineController::class, 'pharmacyDashboard'])->name('pharmacy.dashboard');
});

// Doctor and Admin routes - Patients and Encounters management
Route::middleware(['auth'])->group(function () {
    Route::resource('patients', PatientController::class);
    Route::resource('encounters', EncounterController::class);
});

// AI Prediction Routes - للتنبؤ بالأمراض
Route::middleware(['auth'])->group(function () {
    Route::prefix('ai')->name('ai.')->group(function () {
        Route::get('/dashboard', [AIPredictionController::class, 'dashboard'])->name('dashboard');
        Route::get('/predict', [AIPredictionController::class, 'create'])->name('create');
        Route::post('/predict', [AIPredictionController::class, 'predict'])->name('predict');
        Route::get('/show/{encounterId}', [AIPredictionController::class, 'show'])->name('show');
        Route::get('/pending-review', [AIPredictionController::class, 'pendingReview'])->name('pending-review');
        Route::get('/reports', [AIPredictionController::class, 'reports'])->name('reports');
        Route::post('/review/{prediction}', [AIPredictionController::class, 'review'])->name('review');
        Route::post('/batch-predict', [AIPredictionController::class, 'batchPredict'])->name('batch-predict');
        Route::get('/statistics', [AIPredictionController::class, 'statistics'])->name('statistics');
        Route::get('/health', [AIPredictionController::class, 'healthCheck'])->name('health');
        Route::get('/patient-data/{patient}', [AIPredictionController::class, 'getPatientData'])->name('patient-data');
    });
});

require __DIR__ . '/auth.php';
