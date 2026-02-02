<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SessionController;
use App\Http\Controllers\EventsController;
use App\Http\Controllers\EventActivationController;
use App\Http\Controllers\EventsSignUpController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CheckInController;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['guest'])->group(function () {
    //User management
    Route::get('/attendees/create', [UserController::class, 'create']); //Muestra el formulario de refistro que, en esta versión directamente inscribirá al usuario en un evento dado.
    Route::post('/attendees', [EventsSignUpController::class, 'store']); //Ahora directamente el controlador al que se dirige es aquel dedicado a las inscripciones.
    //Session management
    Route::get('/login', [SessionController::class, 'create'])->name('login');
    Route::post('/login', [SessionController::class, 'store']);
});

Route::middleware(['auth'])->group(function () {
    //Route::get('/attendee/dashboard', [DashboardController::class, 'index'])->name('attendee.dashboard');

    Route::post('/logout', [SessionController::class, 'destroy']);
    //Events
    Route::middleware(['role:admin'])->group(function () {
        Route::get('/events/create', [EventsController::class, 'create'])->name('events.create');
        Route::post('/events', [EventsController::class, 'store']);
        Route::get('/events/{event}/edit', [EventsController::class, 'edit']);
        Route::put('/events/{event}/edit', [EventsController::class, 'update']);
        Route::delete('/events/{event}/delete', [EventsController::class, 'destroy']);
        Route::post('/events/{event}/activate', [EventActivationController::class, 'activate']);
        Route::get('/events/checkin', [CheckInController::class, 'create']);
        Route::post('/events/checkin', [CheckInController::class, 'update']);
        
    });
    Route::get('/events', [EventsController::class, 'index']);
    Route::get('/events/{event}', [EventsController::class, 'show'])->name('events.show');

    //Eliminar?
    //Route::post('/events/signup/{event}/', [EventsSignUpController::class, 'store']);
    //Route::post('events/signup/cancel/{event}/', [EventsSignUpController::class, 'destroy']);
});