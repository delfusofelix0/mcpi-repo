<?php

use App\Http\Controllers\ApplicantController;
use App\Http\Controllers\ApplicantRegistrationController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OfficeController;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\ProfileController;
use App\Models\Office;
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

Route::prefix('appointments')->group(function () {
   Route::get('/', [AppointmentController::class, 'index'])->name('appointments.index');
   Route::post('/', [AppointmentController::class, 'store'])->name('appointments.create');
   Route::get('/reserved-slots', [AppointmentController::class, 'getReservedTimeSlots'])->name('appointments.reserved-slots');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::prefix('appointment-settings')->group(function () {
        Route::get('/', [OfficeController::class, 'index'])
            ->name('offices.index');
        Route::post('/', [OfficeController::class, 'store'])
            ->name('offices.store');
        Route::put('offices/{office}/update-availability', [OfficeController::class, 'updateAvailability'])
            ->name('offices.updateAvailability');
        Route::put('offices/{office}/update-name', [OfficeController::class, 'updateOfficeName'])
            ->name('offices.updateName');
        Route::delete('/{office}', [OfficeController::class, 'destroy'])
            ->name('offices.destroy');
    });
    Route::prefix('dashboard')->group(function () {
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

//Route::get('/applicant/{id}', [ApplicantController::class, 'show'])->name('applicant.show')
//    ->middleware(['auth', 'verified']);



//Route::prefix('dashboard')->group(function () {
//    Route::get('/', function() {
//        return Inertia::render('Dashboard');
//    });
//    Route::get('/', [ApplicantController::class, 'index']);
////    Route::get('/', [PositionController::class, 'index'])->name('dashboard.positions');
//})->middleware(['auth', 'verified']);


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
