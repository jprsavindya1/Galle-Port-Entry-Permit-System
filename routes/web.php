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
use App\Http\Controllers\Admin\YearProcessController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\DashboardController;
use Illuminate\Http\Request;

// Health check endpoint for hosting platforms
Route::get('/health', function () {
    try {
        // Check database connection
        \DB::connection()->getPdo();
        $dbStatus = 'connected';
    } catch (\Exception $e) {
        $dbStatus = 'disconnected';
    }
    
    return response()->json([
        'status' => 'ok',
        'database' => $dbStatus,
        'timestamp' => now()->toIso8601String()
    ], 200);
});

// Root route - check if authenticated, otherwise show login
Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
});

// Old-style inline route — now calls the controller with Request
Route::get('/dashboard', function (Request $request) {
    return app(DashboardController::class)->index($request);
})->middleware('auth')->name('dashboard');

Route::get('/dashboard/data', [DashboardController::class, 'getMonthData'])
    ->middleware('auth')
    ->name('dashboard.data');

// ------------------------------
// Security Routes (Gate Personnel)
// ------------------------------
use App\Http\Controllers\SecurityController;

Route::middleware(['auth', 'role:security'])->prefix('security')->name('security.')->group(function () {
    Route::get('/dashboard', [SecurityController::class, 'index'])->name('dashboard');
    Route::post('/search', [SecurityController::class, 'searchPermit'])->name('search');
});

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

    // Year & Process Management Routes
    Route::get('/year-process', [YearProcessController::class, 'index'])->name('admin.year_process.index');
    Route::put('/year-process/update', [YearProcessController::class, 'update'])->name('admin.year_process.update');
    Route::post('/year-process/start-new-year', [YearProcessController::class, 'startNewYear'])->name('admin.year_process.start_new_year');

    // Admin Activity Logs Route
    Route::get('/admin/activity-logs', [App\Http\Controllers\Admin\ActivityLogController::class, 'index'])->name('admin.activity_logs.index');

    // Admin Database Backup Routes
    Route::get('/admin/backup', [App\Http\Controllers\Admin\BackupController::class, 'index'])->name('admin.backup.index');
    Route::get('/admin/backup/download', [App\Http\Controllers\Admin\BackupController::class, 'download'])->name('admin.backup.download');
});

// ------------------------------
// Clerk and Admin Routes (NOT for security personnel)
// ------------------------------
Route::middleware(['auth', 'role:clerk,admin,super-admin'])->group(function () {

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
Route::post('/permits/fetch-person-details', [PermitController::class, 'fetchPersonDetails'])->name('permit.fetchPersonDetails');
Route::post('/permits/check-blacklist', [PermitController::class, 'checkBlacklist'])->name('permit.checkBlacklist');

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
    Route::post('/fetchVehicleDetails', 'fetchVehicleDetails')->name('permit.vehicle.fetchVehicleDetails');
    Route::get('/edit/{index}', 'editVehicleSessionEntry')->name('permit.vehicle.editVehicleSessionEntry');
    Route::put('/edit/{index}', 'updatevehicleSessionEntry')->name('permit.vehicle.updateVehicleSessionEntry');
    Route::delete('/remove/{index}', 'removevehicleSessionEntry')->name('permit.vehicle.removeVehicleSessionEntry');
}); 


// ------------------------------
// Submitted Permits (clerks)
// ------------------------------
Route::get('/permits/submitted', [PermitController::class, 'submittedList'])->name('permits.submitted');
Route::get('/permits/submitted/{submissionId}', [PermitController::class, 'viewSubmissionGroup'])->name('permit.submission.view');
Route::post('permits/{permitType}/{id}/cancel', [PermitController::class, 'cancel'])->name('permits.cancel');
Route::post('permits/{permitType}/{id}/activate', [PermitController::class, 'activate'])->name('permits.activate');


// ------------------------------
// Edit/Delete Individual Permits (DB Records clerk edits)
// ------------------------------
Route::get('/permits/{permitType}/{id}/edit', [PermitController::class, 'edit'])->name('permits.edit');
Route::put('/permits/{permitType}/{id}', [PermitController::class, 'update'])->name('permits.update');
Route::delete('/permits/{permitType}/{id}', [PermitController::class, 'destroy'])->name('permits.destroy');


// ------------------------------
// Search
// ------------------------------
Route::get('/permits/search', [PermitController::class, 'search'])->name('permits.search');


// ------------------------------
// Black list Management
// ------------------------------
Route::prefix('admin/blacklist')->name('blacklist.')->group(function () {
    Route::get('/', [BlacklistController::class, 'index'])->name('index');
    Route::get('/history', [BlacklistController::class, 'history'])->name('history');
    Route::get('/create', [BlacklistController::class, 'create'])->name('create');
    Route::post('/', [BlacklistController::class, 'store'])->name('store');
    Route::get('/{blacklist}/edit', [BlacklistController::class, 'edit'])->name('edit');
    Route::put('/{blacklist}', [BlacklistController::class, 'update'])->name('update');
    Route::delete('/{blacklist}', [BlacklistController::class, 'destroy'])->name('destroy');
    Route::get('/export/pdf', [App\Http\Controllers\Admin\BlacklistController::class, 'exportPdf'])->name('exportPdf');
    Route::get('/export/excel', [App\Http\Controllers\Admin\BlacklistController::class, 'exportExcel'])->name('exportExcel');
    
});

// Batch print (by submission_id)
Route::get('/permit/print/batch/{submission_id}', [PrintController::class, 'show'])
    ->name('permit.print');

// Single print (by permit ID with type)
Route::get('/permit/print/single/{type}/{id}', [PrintController::class, 'showSingle'])
    ->name('permit.print.single');

    // reports routes

Route::prefix('reports')->group(function () {

    // User Activity Reports
    Route::get('/user', [ReportController::class, 'userReportForm'])->name('reports.user');
    Route::post('/user/results', [ReportController::class, 'userReportResults'])->name('reports.user.results');
    Route::get('/user/export/pdf', [ReportController::class, 'exportUserPdf'])->name('reports.user.pdf');
    Route::get('/user/export/csv', [ReportController::class, 'exportUserCsv'])->name('reports.user.csv');

    // Payment / Financial Reports
    
    Route::get('/payment', [ReportController::class, 'paymentReport'])->name('reports.payment');
    Route::get('/payment/export/pdf', [ReportController::class, 'exportPaymentPdf'])->name('reports.payment.pdf');
    Route::get('/payment/export/csv', [ReportController::class, 'exportPaymentCsv'])->name('reports.payment.csv');
    Route::get('/payment/print/crystal', [ReportController::class, 'printDailyRevenueCrystal'])->name('reports.payment.crystal');
    Route::get('/payment/print/crystal-summary', [ReportController::class, 'printRevenueSummaryCrystal'])->name('reports.payment.crystal_summary');

});

}); // End of clerk,admin,super-admin middleware group

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

Route::middleware('auth')->group(function () {
    Route::get('/profile/edit', [App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
});

Route::middleware(['auth', 'role:admin'])->group(function () {
    
});
