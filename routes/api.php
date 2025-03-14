<?php
use App\Http\Controllers\AdmissionController;
use Illuminate\Support\Facades\Route;

Route::get('/admissions', [AdmissionController::class, 'index']);

Route::get('/admissions', [PatientAdmissionController::class, 'index']);
Route::post('/admissions', [PatientAdmissionController::class, 'store']);
Route::get('/admissions/{id}', [PatientAdmissionController::class, 'show']);
Route::put('/admissions/{id}', [PatientAdmissionController::class, 'update']);
Route::delete('/admissions/{id}', [PatientAdmissionController::class, 'destroy']);
Route::get('/admissions/search', [PatientAdmissionController::class, 'search']);
Route::get('/admissions/export', [PatientAdmissionController::class, 'export']);