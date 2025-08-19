<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\PatientAuthController;
use App\Http\Controllers\Auth\MedecinAuthController;
use App\Http\Controllers\MedecinController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\MessageController;



// ======================
// Routes Public
// ======================
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Récupérer tous les médecins
Route::get('/medecins', [MedecinController::class, 'index']);
Route::get('/medecins/{id}', [MedecinController::class, 'show']);


// ======================
// Routes Patient
// ======================
Route::prefix('patient')->group(function () {
    Route::post('/register', [PatientAuthController::class, 'register']);
    Route::post('/login', [PatientAuthController::class, 'login']);

    // Routes sécurisées pour le profil patient
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/profile', [PatientAuthController::class, 'profile']);
        Route::put('/profile', [PatientAuthController::class, 'updateProfile']);
    });
});

Route::middleware('auth:medecin')->get('/medecin/appointments', [AppointmentController::class, 'doctorAppointments']);
Route::middleware('auth:medecin')->patch('/medecin/appointments/{id}/confirm', [AppointmentController::class, 'confirm']);
Route::middleware('auth:medecin')->patch('/medecin/appointments/{id}/reject', [AppointmentController::class, 'reject']);


// ======================
// Routes Médecin
// ======================
Route::prefix('medecin')->group(function () {
    Route::post('/register', [MedecinAuthController::class, 'register']);
    Route::post('/login', [MedecinAuthController::class, 'login']);
});

// Routes sécurisées pour le profil medecin
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/medecin/profile', [MedecinAuthController::class, 'profile']);
    Route::put('/medecin/profile', [MedecinAuthController::class, 'updateProfile']);
});
Route::middleware('auth:sanctum')->put('/medecin/working-hours', [MedecinController::class, 'updateWorkingHours']);

Route::middleware('auth:patient')->post('/appointments', [AppointmentController::class, 'store']);
Route::middleware('auth:patient')->get('/patient/appointments', [AppointmentController::class, 'index']);

Route::middleware('auth:patient')->group(function () {
    Route::get('/messages/{medecinId}', [MessageController::class, 'getMessages']);
    Route::post('/messages/send', [MessageController::class, 'sendMessage']);
});
