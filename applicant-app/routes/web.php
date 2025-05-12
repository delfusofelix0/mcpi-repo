<?php

use App\Http\Controllers\ApplicantController;
use App\Http\Controllers\ApplicantRegistrationController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\AppointmentSettingController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OfficeController;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\ProfileController;
//
use App\Http\Controllers\QMS\AccountingController;
use App\Http\Controllers\QMS\DepartmentController;
use App\Http\Controllers\QMS\RegistrarController;
use App\Http\Controllers\QMS\TicketController;
use App\Http\Controllers\QMS\DisplayController;
use App\Http\Controllers\QMS\CashierController;
use App\Http\Controllers\QMS\AdminWindowController;
//
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::prefix('applicant-form')->group(function () {
    Route::get('/', [ApplicantRegistrationController::class, 'workPosition'])->name('applicant-form.workPosition');
    Route::post('/', [ApplicantRegistrationController::class, 'store'])->name('applicant-form.store');
});

Route::prefix('appointment')->group(function () {
   Route::get('/', [AppointmentController::class, 'index'])->name('appointments.index');
   Route::post('/', [AppointmentController::class, 'store'])->name('appointments.create');
   Route::get('/reserved-slots', [AppointmentController::class, 'getReservedTimeSlots'])->name('appointments.reserved-slots');
   Route::post('/{appointment}/send-sms', [AppointmentController::class,'sendSms'])->middleware(['auth', 'verified','role:Secretary|Admin'])
        ->name('appointments.send-sms');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::prefix('appointment-settings')->middleware(['role:Secretary|Admin'])->group(function () {
        // Getting the list offices and appointments
        Route::get('/', [AppointmentSettingController::class, 'index'])
            ->name('appointment-settings.index');
        // Office Management Side
        Route::post('/', [OfficeController::class, 'store'])
            ->name('offices.store');
        Route::put('offices/{office}/update-availability', [OfficeController::class, 'updateAvailability'])
            ->name('offices.updateAvailability');
        Route::put('offices/{office}/update-name', [OfficeController::class, 'updateOfficeName'])
            ->name('offices.updateName');
        Route::delete('/{office}', [OfficeController::class, 'destroy'])
            ->name('offices.destroy');
    });
    Route::prefix('dashboard')->middleware(['role:HR|Admin'])->group(function () {
        Route::get('/', [DashboardController::class, 'index'])
            ->name('dashboard'); //registrations and position list
        Route::post('/applicant/{id}', [ApplicantController::class, 'statusStore'])
            ->name('applicant.statusStore');
        Route::delete('/applicant/{id}', [ApplicantController::class, 'destroyApplicant'])
            ->name('applicant.destroyApplicant')
            ->middleware('role:Admin');
        Route::post('/', [PositionController::class, 'store'])
            ->name('positions.store');
        Route::put('/position/{id}', [PositionController::class, 'update'])
            ->name('positions.update');
        Route::delete('/position/{id}', [PositionController::class, 'destroy'])
            ->name('positions.destroy');
        Route::get('/show-applicant/{id}', [ApplicantController::class, 'show'])
            ->name('applicant.show');
    });
    Route::post('/applicant/{id}/send-sms', [ApplicantController::class, 'sendSms'])
        ->name('applicant.send-sms');
});

// Public routes
Route::middleware(['display.token'])->group(function() {
    Route::get('/tickets', [TicketController::class, 'index'])->name('tickets.index');
    Route::post('/tickets/generate', [TicketController::class, 'generate'])->name('tickets.generate');
    Route::get('/display', [DisplayController::class, 'index'])->name('display.index');
    Route::get('/api/display-tickets', [DisplayController::class, 'getCurrentTickets'])->name('api.display-tickets');
});

// QMS routes (protected by auth)
Route::middleware(['auth', 'verified'])->group(function () {
    // Department route
    Route::post('/select-window', [DepartmentController::class, 'selectWindow'])->name('select-window');

    Route::prefix('department')->name('department.')->group(function () {
        Route::post('/call-next', [DepartmentController::class, 'callNext'])->name('call-next');
        Route::post('/complete/{ticket}', [DepartmentController::class, 'complete'])->name('complete');
        Route::post('/skip/{ticket}', [DepartmentController::class, 'skip'])->name('skip');
        Route::post('/select-window', [DepartmentController::class, 'selectWindow'])->name('select-window');
    });

    // Cashier routes
    Route::prefix('cashier')->name('cashier.')->middleware(['role:Cashier'])->group(function () {
        Route::get('/dashboard', [CashierController::class, 'dashboard'])->name('dashboard');
//        Route::post('/call-next', [CashierController::class, 'callNext'])->name('call-next');
//        Route::post('/complete/{ticket}', [CashierController::class, 'complete'])->name('complete');
//        Route::post('/skip/{ticket}', [CashierController::class, 'skip'])->name('skip');
    });

    // Accounting routes
    Route::prefix('accounting')->name('accounting.')->middleware(['role:Accounting'])->group(function () {
        Route::get('/dashboard', [AccountingController::class, 'dashboard'])->name('dashboard');
//        Route::post('/call-next', [AccountingController::class, 'callNext'])->name('call-next');
//        Route::post('/complete/{ticket}', [AccountingController::class, 'complete'])->name('complete');
//        Route::post('/skip/{ticket}', [AccountingController::class, 'skip'])->name('skip');
    });

    // Registrar routes
    Route::prefix('registrar')->name('registrar.')->middleware(['role:Registrar'])->group(function () {
        Route::get('/dashboard', [RegistrarController::class, 'dashboard'])->name('dashboard');
//        Route::post('/call-next', [RegistrarController::class, 'callNext'])->name('call-next');
//        Route::post('/complete/{ticket}', [RegistrarController::class, 'complete'])->name('complete');
//        Route::post('/skip/{ticket}', [RegistrarController::class, 'skip'])->name('skip');
    });
});

// Admin routes (protected by auth + admin check)
//Route::prefix('admin')->name('admin.')->group(function () {
//    Route::get('/windows', [AdminWindowController::class, 'index'])->name('windows.index');
//    Route::post('/windows', [AdminWindowController::class, 'store'])->name('windows.store');
//    Route::put('/windows/{window}', [AdminWindowController::class, 'update'])->name('windows.update');
//});


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
