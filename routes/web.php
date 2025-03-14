<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdmissionController;

Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index']);
Route::resource('admissions', AdmissionController::class);
Route::post('/admissions/import', [AdmissionController::class, 'import'])->name('admissions.import');


// Routes pour le système de prédiction
Route::get('/predictions', [App\Http\Controllers\PredictionController::class, 'index'])->name('predictions.index');
Route::post('/predictions/calculate', [App\Http\Controllers\PredictionController::class, 'predict'])->name('predictions.calculate');
Route::get('/predictions/next15days', [App\Http\Controllers\PredictionController::class, 'next15Days'])->name('predictions.next15days');