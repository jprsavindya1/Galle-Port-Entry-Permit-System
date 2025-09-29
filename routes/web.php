<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PermitController;
use App\Http\Controllers\TemporaryPermitController;
use App\Http\Controllers\MonthlyPermitController;
use App\Http\Controllers\VehiclePermitController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\PaymentSettingController;
use App\Http\Controllers\PrintController;
use App\Http\Controllers\Admin\CompanyController;
use App\Http\Controllers\Admin\DesignationController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\ReasonController;
use App\Http\Controllers\Admin\VehicleController;
use App\Http\Controllers\Admin\BlacklistController;
use App\Http\Controllers\Admin\CancelledPermitController;
use App\Http\Controllers\ReportController;
// ------------------------------
//Admin Routes
// ------------------------------
Route::middleware(['auth', 'role:admin,super-admin'])->group(function () {
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    

    Route::get('/payment-settings/edit', [PaymentSettingController::class, 'edit'])->name('admin.payment_settings.edit');
    Route::put('/payment-settings/update', [PaymentSettingController::class, 'update'])->name('admin.payment_settings.update');


// active permit delete Routes
    Route::delete('/permit/remove/{index}', [TemporaryPermitController::class, 'removeEntry'])->name('permit.remove');
    
// Master Data Routes
    Route::resource('reasons', ReasonController::class)->names('admin.reasons');
    Route::resource('vehicles', VehicleController::class)->names('admin.vehicles');
    Route::resource('companies', CompanyController::class)->names('admin.companies');
    Route::resource('designations', DesignationController::class)->names('admin.designations');
    Route::get('/admin/masterdata', function () {
        return view('admin.masterdata.index');
    })->name('admin.masterdata');

   Route::prefix('admin/cancelled_permits')
    ->name('admin.cancelled_permits.')
    ->group(function () {

        // List cancelled permits
        Route::get('/', [CancelledPermitController::class, 'index'])->name('index');

        // Export routes
        Route::get('/export-excel', [CancelledPermitController::class, 'exportExcel'])->name('exportExcel');
        Route::get('/export-pdf', [CancelledPermitController::class, 'exportPdf'])->name('exportPdf');

        // Show only trashed (soft deleted) permits
        Route::get('/trash', [CancelledPermitController::class, 'trash'])->name('trash');

        // Permanently delete a trashed permit (DELETE)
        Route::delete('/{id}/force-delete', [CancelledPermitController::class, 'forceDelete'])->name('forceDelete');

        // Restore a trashed permit (POST)
        Route::post('/{id}/restore', [CancelledPermitController::class, 'restore'])->name('restore');

        // Activate a cancelled permit (POST)
        Route::post('/{permit}/activate', [CancelledPermitController::class, 'activate'])->name('activate');

        // Cancel an active permit (POST)
        Route::post('/{permit}/cancel', [CancelledPermitController::class, 'cancel'])->name('cancel');

        // Show single cancelled permit
        Route::get('/{id}', [CancelledPermitController::class, 'show'])->name('show');

        // Delete a cancelled permit entry (soft delete)
        Route::delete('/{id}', [CancelledPermitController::class, 'destroy'])->name('destroy');

    });


});



Route::get('/', fn () => redirect()->route('dashboard'));
Route::get('/dashboard', fn () => view('dashboard'))->middleware('auth')->name('dashboard');


// ------------------------------
// invoice Routes
// ------------------------------
Route::get('/payment/summary', [PaymentController::class, 'summary'])->name('payment.summary');
Route::post('/payment/submit', [PaymentController::class, 'submit'])->name('payment.submit');
Route::get('/payment/invoice/{submission_id}', [PaymentController::class, 'invoice'])->name('payment.invoice');


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

Route::prefix('monthly-permit')->controller(MonthlyPermitController::class)->group(function () {
    Route::get('/', 'createMonthly')->name('permit.monthly'); // Main page
    Route::post('/add', 'addMonthlyToSession')->name('permit.monthly.addMonthlyToSession');
    Route::get('/summary', 'showMonthlySummary')->name('permit.monthly.summary');
    Route::post('/submit', 'submitAllMonthly')->name('permit.monthly.submitAll');
    Route::get('/edit/{index}', 'editMonthlySessionEntry')->name('permit.monthly.editMonthlySessionEntry');
    Route::put('/edit/{index}', 'updateMonthlySessionEntry')->name('permit.monthly.updateMonthlySessionEntry');
    Route::delete('/remove/{index}', 'removeMonthlySessionEntry')->name('permit.monthly.removeMonthlySessionEntry');
    Route::post('/check-availability', 'checkMonthlyAvailability')->name('permit.monthly.checkMonthlyAvailability');
});



// ------------------------------
// Vehicle Permit Routes
// ------------------------------
Route::prefix('permit/vehicle')->controller(VehiclePermitController::class)->group(function () {
    Route::get('/', 'createVehicle')->name('permit.vehicle');
    Route::post('/add', 'addVehicleToSession')->name('permit.vehicle.addToSession');
    Route::get('/edit-session-entry/{index}', 'editVehicleSessionEntry')->name('permit.vehicle.editSessionEntry');
    Route::put('/update-session-entry/{index}', 'updateVehicleSessionEntry')->name('permit.vehicle.updateSessionEntry');
    Route::post('/submit', 'submitAllVehicle')->name('permit.vehicle.submitAllVehicle');
    Route::post('/checkAvailability', 'checkVehicleAvailability')->name('permit.vehicle.checkVehicleAvailability');
    Route::get('/edit/{index}', 'editVehicleSessionEntry')->name('permit.vehicle.editVehicleSessionEntry');
    Route::put('/edit/{index}', 'updatevehicleSessionEntry')->name('permit.vehicle.updateVehicleSessionEntry');
    Route::delete('/remove/{index}', 'removevehicleSessionEntry')->name('permit.vehicle.removeVehicleSessionEntry');
}); 


// ------------------------------
// Submitted Permits (clerks)
// ------------------------------
Route::get('/permits/submitted', [PermitController::class, 'submittedList'])->name('permits.submitted');
Route::get('/permits/submitted/{submissionId}', [PermitController::class, 'viewSubmissionGroup'])->name('permit.submission.view');
Route::post('permits/{permit}/cancel', [PermitController::class, 'cancel'])->name('permits.cancel');
Route::post('permits/{permit}/activate', [PermitController::class, 'activate'])->name('permits.activate');


// ------------------------------
// Edit/Delete Individual Permits (DB Records clerk edits)
// ------------------------------
Route::get('/permits/{permit}/edit', [PermitController::class, 'edit'])->name('permits.edit');
Route::put('/permits/{permit}', [PermitController::class, 'update'])->name('permits.update');
Route::delete('/permits/{permit}', [PermitController::class, 'destroy'])->name('permits.destroy');


// ------------------------------
// Search
// ------------------------------
Route::get('/permits/search', [PermitController::class, 'search'])->name('permits.search');


// ------------------------------
// Black list Management
// ------------------------------
Route::prefix('admin/blacklist')->middleware('auth')->name('blacklist.')->group(function () {
    Route::get('/', [BlacklistController::class, 'index'])->name('index');
    Route::get('/create', [BlacklistController::class, 'create'])->name('create');
    Route::post('/', [BlacklistController::class, 'store'])->name('store');
    Route::get('/{blacklist}/edit', [BlacklistController::class, 'edit'])->name('edit');
    Route::put('/{blacklist}', [BlacklistController::class, 'update'])->name('update');
    Route::delete('/{blacklist}', [BlacklistController::class, 'destroy'])->name('destroy');
});

// Batch print (by submission_id)
Route::get('/permit/print/batch/{submission_id}', [PrintController::class, 'show'])
    ->name('permit.print');

// Single print (by permit ID)
Route::get('/permit/print/single/{id}', [PrintController::class, 'showSingle'])
    ->name('permit.print.single');

    // reports routes

Route::prefix('reports')->middleware('auth')->group(function () {

    // User Activity Reports
    Route::get('/user', [ReportController::class, 'userReportForm'])->name('reports.user');
    Route::post('/user/results', [ReportController::class, 'userReportResults'])->name('reports.user.results');
    Route::get('/user/export/pdf', [ReportController::class, 'exportUserPdf'])->name('reports.user.pdf');
    Route::get('/user/export/csv', [ReportController::class, 'exportUserCsv'])->name('reports.user.csv');

    // Payment / Financial Reports
    
    Route::get('/payment', [ReportController::class, 'paymentReport'])->name('reports.payment');
    Route::get('/payment/export/pdf', [ReportController::class, 'exportPaymentPdf'])->name('reports.payment.pdf');
    Route::get('/payment/export/csv', [ReportController::class, 'exportPaymentCsv'])->name('reports.payment.csv');

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

Route::middleware(['auth', 'role:admin'])->group(function () {
    
});
