<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PermitController;
use App\Http\Controllers\TemporaryPermitController;
use App\Http\Controllers\MonthlyPermitController;
use App\Http\Controllers\VehiclePermitController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Admin\PaymentSettingController;

Route::middleware(['auth', 'role:admin,super-admin'])->group(function () {
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    Route::get('/payment-settings/edit', [PaymentSettingController::class, 'edit'])->name('admin.payment_settings.edit');
    Route::put('/payment-settings/update', [PaymentSettingController::class, 'update'])->name('admin.payment_settings.update');
    Route::delete('/permit/remove/{index}', [TemporaryPermitController::class, 'removeEntry'])->name('permit.remove');

});


Route::get('/', fn () => redirect()->route('dashboard'));
Route::get('/dashboard', fn () => view('dashboard'))->middleware('auth')->name('dashboard');


Route::get('/payment/summary', [\App\Http\Controllers\PaymentController::class, 'summary'])->name('payment.summary');
Route::post('/payment/submit', [\App\Http\Controllers\PaymentController::class, 'submit'])->name('payment.submit');



// ------------------------------
// Temporary Permit Routes
// ------------------------------
Route::prefix('temporary-permit')->controller(TemporaryPermitController::class)->group(function () {
    Route::get('/', 'createTemporary')->name('permit.temporary');
    Route::post('/add', 'addToSession')->name('permit.addToSession');
    Route::get('/summary', 'showSummary')->name('permit.summary');
    Route::post('/submit', 'submitAll')->name('permit.submitAll');
    Route::get('/edit/{index}', 'editSessionEntry')->name('permit.editSessionEntry');
    Route::put('/edit/{index}', 'updateSessionEntry')->name('permit.updateSessionEntry');
    Route::delete('/remove/{index}', 'removeTemporaryEntry')->name('permit.removeSessionEntry');

});

Route::post('/permits/check-availability', [PermitController::class, 'checkAvailability'])->name('permit.checkAvailability');


// ------------------------------
// Monthly Permit Routes
// ------------------------------
Route::prefix('permit/monthly')->controller(MonthlyPermitController::class)->group(function () {
    Route::get('/', 'createMonthly')->name('permit.monthly');
    Route::post('/add', 'addMonthlyToSession')->name('permit.monthly.addToSession');
    Route::get('/summary', 'showMonthlySummary')->name('permit.monthly.summary');
    Route::post('/submit', 'submitAllMonthly')->name('permit.monthly.submit');
    Route::post('/checkAvailability', 'checkMonthlyAvailability')->name('permit.monthly.checkAvailability');
    Route::get('/edit-session-entry/{index}', 'editMonthlySessionEntry')->name('permit.monthly.editSessionEntry');
    Route::put('/update-session-entry/{index}', 'updateMonthlySessionEntry')->name('permit.monthly.updateSessionEntry');
    
});


// ------------------------------
// Vehicle Permit Routes
// ------------------------------
Route::prefix('permit/vehicle')->controller(VehiclePermitController::class)->group(function () {
    Route::get('/', 'createVehicle')->name('permit.vehicle');
    Route::post('/add', 'addVehicleToSession')->name('permit.vehicle.addToSession');
    Route::get('/edit-session-entry/{index}', 'editVehicleSessionEntry')->name('permit.vehicle.editSessionEntry');
    Route::put('/update-session-entry/{index}', 'updateVehicleSessionEntry')->name('permit.vehicle.updateSessionEntry');
    Route::post('/submit', 'submitAllVehicle')->name('permit.vehicle.submitAll');
    Route::post('/checkAvailability', 'checkVehicleAvailability')->name('permit.vehicle.checkAvailability');
});


// ------------------------------
// Submitted Permits (Admin)
// ------------------------------
Route::get('/permits/submitted', [PermitController::class, 'submittedList'])->name('permits.submitted');
Route::get('/permits/submitted/{submissionId}', [PermitController::class, 'viewSubmissionGroup'])->name('permit.submission.view');


// ------------------------------
// Edit/Delete Individual Permits (DB Records)
// ------------------------------
Route::get('/permits/{permit}/edit', [PermitController::class, 'edit'])->name('permits.edit');
Route::put('/permits/{permit}', [PermitController::class, 'update'])->name('permits.update');
Route::delete('/permits/{permit}', [PermitController::class, 'destroy'])->name('permits.destroy');


// ------------------------------
// Search
// ------------------------------
Route::get('/permits/search', [PermitController::class, 'search'])->name('permits.search');

use App\Http\Controllers\Admin\BlacklistController;

Route::prefix('admin/blacklist')->middleware('auth')->name('blacklist.')->group(function () {
    Route::get('/', [BlacklistController::class, 'index'])->name('index');
    Route::get('/create', [BlacklistController::class, 'create'])->name('create');
    Route::post('/', [BlacklistController::class, 'store'])->name('store');
    Route::get('/{blacklist}/edit', [BlacklistController::class, 'edit'])->name('edit');
    Route::put('/{blacklist}', [BlacklistController::class, 'update'])->name('update');
    Route::delete('/{blacklist}', [BlacklistController::class, 'destroy'])->name('destroy');
});



Route::middleware(['role:admin'])->group(function () {
    Route::get('/admin', function () {
        return 'Welcome Admin!';
    });
});

Route::middleware(['role:admin,staff'])->group(function () {
    Route::get('/admin-or-staff', function () {
        return 'Admins and Staff only!';
    });
});

require __DIR__.'/auth.php';

Route::get('/profile/edit', fn () => 'Edit profile page coming soon')->name('profile.edit');
