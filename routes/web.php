<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\SpecialtyController;

/**
 * Rotas públicas
 */
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');
});

/**
 * Rotas de Paciente
 */
Route::middleware('auth')->group(function () {
    // Listagem de médicos e horiários disponíveis
    Route::get('/doctors', [DoctorController::class, 'index'])->name('doctors.index');
    Route::get('/doctors/{id}/schedules', [DoctorController::class, 'avaliableTimes'])->name('doctors.schedules');

    // Agendamentos e Minhas Consultas
    Route::get('/my-appointments', [AppointmentController::class, 'myAppointments'])->name('appointments.index');
    Route::post('/store', [AppointmentController::class, 'store'])->name('appointment.store');
    Route::delete('/appointments/{id}/cancel', [AppointmentController::class, 'cancel'])->name('appointments.cancel');
});

/**
 * Rotas Administrativas
 */
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // CRUD de Especialidades
    Route::resource('specialties', SpecialtyController::class);

    // CRUD de Médicos
    Route::resource('doctors', DoctorController::class);

    // Visão Geral de Agendamentos e Alteração de Status
    Route::get('/appointments', [AppointmentController::class, 'index'])->name('appoinments.index');
    Route::patch('/appointments/{id}/status', [AppointmentController::class, 'updateStatus'])->name('appointments.updateStatus');
});