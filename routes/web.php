<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PermitController;

// ------------------------------
// Temporary Permit Routes
// ------------------------------
Route::get('/temporary-permit', [PermitController::class, 'createTemporary'])->name('permit.temporary');
Route::post('/temporary-permit/add', [PermitController::class, 'addToSession'])->name('permit.addToSession');
Route::get('/temporary-permit/summary', [PermitController::class, 'showSummary'])->name('permit.summary');
Route::post('/temporary-permit/submit', [PermitController::class, 'submitAll'])->name('permit.submitAll');

// Temporary permit session editing
Route::get('/temporary-permit/edit/{index}', [PermitController::class, 'editSessionEntry'])->name('permit.editSessionEntry');
Route::put('/temporary-permit/edit/{index}', [PermitController::class, 'updateSessionEntry'])->name('permit.updateSessionEntry');

// Availability check
Route::post('/permit/checkAvailability', [PermitController::class, 'checkAvailability'])->name('permit.checkAvailability');

// ------------------------------
// Monthly Permit Routes
// ------------------------------
Route::get('/permit/monthly', [PermitController::class, 'createMonthly'])->name('permit.monthly');
Route::post('/permit/monthly/add', [PermitController::class, 'addMonthlyToSession'])->name('permit.monthly.addToSession');
Route::get('/permit/monthly/summary', [PermitController::class, 'showMonthlySummary'])->name('permit.monthly.summary');
Route::post('/permit/monthly/submit', [PermitController::class, 'submitAllMonthly'])->name('permit.monthly.submit');
Route::post('/permit/monthly/checkAvailability', [PermitController::class, 'checkMonthlyAvailability'])->name('permit.monthly.checkAvailability');

// Show edit form for monthly permit session entry
    Route::get('permit/monthly/edit-session-entry/{index}', [PermitController::class, 'editMonthlySessionEntry'])
    ->name('permit.monthly.editSessionEntry');

// Update the edited monthly permit session entry
Route::put('permit/monthly/update-session-entry/{index}', [PermitController::class, 'updateMonthlySessionEntry'])
    ->name('permit.monthly.updateSessionEntry');

// ------------------------------
// Vehicle Permit
Route::get('/permit/vehicle', [PermitController::class, 'createVehicle'])->name('permit.vehicle');
Route::post('/permit/vehicle/add', [PermitController::class, 'addVehicleToSession'])->name('permit.vehicle.addToSession');
Route::get('/permit/vehicle/edit-session-entry/{index}', [PermitController::class, 'editVehicleSessionEntry'])->name('permit.vehicle.editSessionEntry');
Route::put('/permit/vehicle/update-session-entry/{index}', [PermitController::class, 'updateVehicleSessionEntry'])->name('permit.vehicle.updateSessionEntry');
Route::post('/permit/vehicle/submit', [PermitController::class, 'submitAllVehicle'])->name('permit.vehicle.submitAll');
Route::post('/permit/vehicle/checkAvailability', [PermitController::class, 'checkVehicleAvailability'])->name('permit.vehicle.checkAvailability');

Route::get('/permit/vehicle/edit-session-entry/{index}', [PermitController::class, 'editVehicleSessionEntry'])->name('permit.vehicle.editSessionEntry');
Route::put('/permit/vehicle/update-session-entry/{index}', [PermitController::class, 'updateVehicleSessionEntry'])->name('permit.vehicle.updateSessionEntry');


// ------------------------------
// Submitted Permits Dashboard (Admin)
// ------------------------------
Route::get('/permits/submitted', [PermitController::class, 'submittedList'])->name('permits.submitted');
Route::get('/permits/submitted/{submissionId}', [PermitController::class, 'viewSubmissionGroup'])->name('permit.submission.view');

// ------------------------------
// Edit/Delete Individual Permits
// ------------------------------
Route::get('/permits/{permit}/edit', [PermitController::class, 'edit'])->name('permits.edit');
Route::put('/permits/{permit}', [PermitController::class, 'update'])->name('permits.update');
Route::delete('/permits/{permit}', [PermitController::class, 'destroy'])->name('permits.destroy');

// ------------------------------
// Search
// ------------------------------
Route::get('/permits/search', [PermitController::class, 'search'])->name('permits.search');
