<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SessionController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\EditionController;
use App\Http\Controllers\ClientPortfolioController;
use App\Http\Controllers\GuestListController;
use App\Http\Controllers\InvitationListController;
use App\Http\Controllers\FormController;

Route::get('/', [SessionController::class, 'create'])->name('home');
Route::post('/', [SessionController::class, 'store'])->name('login');
Route::get('/event/{event}/signup-form', [FormController::class, 'create'], )->name('event-signup');
Route::post('/event/{event}/signup-form', [FormController::class, 'store'])->name('event-signup-store');
Route::get('privacy-policy', function(){
    return view('privacy-policy');
})->name('privacy-policy');
Route::get('/signup-success', function(){
    return view('form.success');
})->name('form-success');

Route::middleware(['admin:admin'])->group(function(){
    Route::get('/events/create', [EventController::class, 'create'])->name('events-create');
    Route::post('/events/create', [EventController::class, 'store'])->name('events-store');
    Route::get('/event/{event}/edit', [EventController::class, 'edit'])->name('events-edit');
    Route::patch('event/{event}/edit', [EventController::class, 'update'])->name('events-update');
    Route::delete('/event/{event}/delete', [EventController::class, 'destroy'])->name('events-delete');
    Route::get('/event/{event}/edition', [EditionController::class, 'create'])->name('editions-create');
    Route::post('/event/{event}/edition', [EditionController::class, 'store'])->name('editions-store');
    Route::patch('/edition/{edition}', [EditionController::class, 'update'])->name('editions-update');
    Route::delete('/edition/{edition}', [EditionController::class, 'destroy'])->name('editions-delete');
    Route::post('/edition/{edition}/assign-manager', [EditionController::class, 'assignManager'])->name('assign-user');
    Route::get('/edition/{edition}/attendees', [EditionController::class, 'attendees'])->name('edition-attendees');
});

Route::middleware(['auth'])->group(function (){
    Route::get('/dashboard', [AuthController::class, 'create'])->name('dashboard');
    Route::post('/logout', [AuthController::class, 'destroy'])->name('logout');
    Route::get('/events/index', [EventController::class, 'index'])->name('events-index');
    Route::get('/events/show/{event}', [EventController::class, 'show'])->name('events-show');
    Route::get('/edition/{edition}', [EditionController::class, 'edit'])->name('editions-edit');
    Route::get('/edition/{edition}/guest-list/create', [GuestListController::class, 'create'])->name('guest-list-create');
    Route::get('/user/{id}/portfolio', [ClientPortfolioController::class, 'index'])->name('portfolios-index');
    Route::get('/portfolio/{portfolio}', [ClientPortfolioController::class, 'show'])->name('portfolios-show');
    Route::get('/user/{user}/editions', [EditionController::class, 'managerEditions'])->name('manager-editions');
    Route::get('/edition/{edition}/invitations', [InvitationListController::class, 'create'])->name('edition-invitation-list');
    Route::post('/edition/{edition}/invitations', [InvitationListController::class, 'store'])->name('invitation-list-store');
    Route::get('/user/{id}/invitation-list', [InvitationListController::class, 'index'])->name('invitation-lists-index');
    Route::get('/invitation-list/{list}', [InvitationListController::class, 'show'])->name('invitation-list-show');
    Route::patch('/invitation-list/{list}', [InvitationListController::class, 'update'])->name('invitation-list-update');
});

