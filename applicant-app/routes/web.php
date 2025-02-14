<?php

use App\Http\Controllers\ApplicantController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RegistrationController;
use Illuminate\Support\Facades\Route;
use App\View\Components\ViewApplicant;

Route::get('/applicant/{id}', function($id) {
    return view('view-applicant', ['id' => $id]);
})->name('applicant.view');

Route::get('/', function () {
    return view('welcome');
});

//Route::get('/applicant-form', function () {
//    return view('applicant-form');
//})->name('applicant.form');

Route::post('/applicant-form', [RegistrationController::class, 'store'])->name('applicant.store');
Route::get('/applicant-form', [RegistrationController::class, 'viewPosition'])->name('applicant.viewPosition');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/applicant/{id}', [ApplicantController::class, 'view'])
    ->middleware(['auth', 'verified'])->name('applicant.view');
Route::get('/dashboard', [ApplicantController::class, 'index'])
    ->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
