<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SessionController;
use App\Http\Controllers\EventsController;

Route::get('/', function () {
    return view('welcome');
});

 //Events
Route::get('/events', [EventsController::class, 'index']);
Route::get('/events/{event}', [EventsController::class, 'show'])->name('events.show');

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
});

Route::middleware(['auth', 'role'])->group(function () {
    //Events
    Route::get('/events/create', [EventsController::class, 'create']);
    Route::post('/events', [EventsController::class, 'store']);
    Route::get('/events/{event}/edit', [EventsController::class, 'edit']);
});

Route::resource('attendees', UserController::class);