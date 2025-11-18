<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SessionController;
use App\Http\Controllers\EventsController;
use App\Http\Controllers\EventActivationController;
use App\Http\Controllers\EventsSignUpController;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['guest'])->group(function () {
    //User management
    Route::get('/attendees/create', [UserController::class, 'create']);
    Route::post('/attendees', [UserController::class, 'store']);
    //Session management
    Route::get('/login', [SessionController::class, 'create'])->name('login');
    Route::post('/login', [SessionController::class, 'store']);
});

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        return view('attendee.dashboard', ['user' => auth()->user()]);
        })->name('dashboard');

    Route::post('/logout', [SessionController::class, 'destroy']);
    //Events
    Route::middleware(['role:admin'])->group(function () {
        Route::get('/events/create', [EventsController::class, 'create'])->name('events.create');
        Route::post('/events', [EventsController::class, 'store']);
        Route::get('/events/{event}/edit', [EventsController::class, 'edit']);
        Route::post('/events/{event}/activate', [EventActivationController::class, 'activate']);
        
    });
    Route::get('/events', [EventsController::class, 'index']);
    Route::get('/events/{event}', [EventsController::class, 'show'])->name('events.show');

    Route::post('/events/signup/{eventId}/', [EventsSignUpController::class, 'store']);
});