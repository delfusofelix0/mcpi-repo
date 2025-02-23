<?php

use App\Http\Controllers\ApplicantController;
use App\Http\Controllers\ApplicantRegistrationController;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\ProfileController;
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

////route applicantForm view with name
//Route::get('/applicant-form', function () {
//    return Inertia::render('ApplicantForm');
//})->name('applicant-form');
//
Route::middleware(['auth', 'verified'])->group(function () {
    Route::prefix('dashboard')->group(function () {
        Route::get('/', [ApplicantController::class, 'index'])->name('dashboard');
        Route::post('/{id}', [ApplicantController::class, 'statusStore'])->name('applicant.statusStore');
        Route::delete('/{id}', [ApplicantController::class, 'destroy'])->name('applicant.destroy');
    });
});

Route::get('/applicant/{id}', [ApplicantController::class, 'show'])->name('applicant.show')
    ->middleware(['auth', 'verified']);



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

//Route::middleware(['role:HR|Admin'])->group(function () {
//    Route::get('/applicants', [ApplicantController::class, 'index']);
//});

//Route::middleware(['role:Admin'])->group(function () {
//    Route::get('/users', [UserController::class, 'index']);
//});

//Route::middleware(['auth', 'role:Admin'])->group(function () {
//    Route::get('/admin-dashboard', function () {
//        return Inertia::render('AdminDashboard');
//    });
//});
