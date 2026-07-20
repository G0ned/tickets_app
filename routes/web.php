<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SessionController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\EditionController;
use App\Http\Controllers\ClientPortfolioController;
use App\Http\Controllers\GuestListController;
use App\Http\Controllers\InvitationListController;
use App\Http\Controllers\InvitationRegistrationController;
use App\Http\Controllers\FormController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CheckInController;
use App\Http\Controllers\PersonController;
//Home route
Route::get('/', [SessionController::class, 'create'])->name('home');
//Login route
Route::post('/', [SessionController::class, 'store'])->name('login');
//Event sign-up route
Route::get('/event/{event}/signup-form', [FormController::class, 'create'], )->name('event-signup');
Route::post('/event/{event}/signup-form', [FormController::class, 'store'])->name('event-signup-store');
//Invitation registration routes (public: the invited person has no account)
Route::get('/invitations/{token}', [InvitationRegistrationController::class, 'create'])->name('invitation-registration-create');
Route::post('/invitations/{token}', [InvitationRegistrationController::class, 'store'])->name('invitation-registration-store');
//Policies routes
Route::get('privacy-policy', function(){
    return view('privacy-policy');
})->name('privacy-policy');
Route::get('/signup-success', function(){
    return view('form.success');
})->name('form-success');

Route::middleware(['admin:admin'])->group(function(){
    //Event routes
    Route::get('/events/create', [EventController::class, 'create'])->name('events-create');
    Route::post('/events/create', [EventController::class, 'store'])->name('events-store');
    Route::post('events/{event}/assign-organizer', [EventController::class, 'assignOrganizer'])->name('assign-organizer');
    Route::delete('/event/{event}/delete', [EventController::class, 'destroy'])->name('events-delete');
    //Edition routes
    Route::delete('/edition/{edition}', [EditionController::class, 'destroy'])->name('editions-delete');
    Route::post('/edition/{edition}/assign-manager', [EditionController::class, 'assignManager'])->name('assign-user');
    Route::get('/edition/{edition}/attendees', [EditionController::class, 'attendees'])->name('edition-attendees');
    Route::get('/edition/{edition}/export-attendees', [EditionController::class, 'exportAttendees'])->name('export-attendees');
    //User routes
    Route::get('/user/create', [UserController::class, 'create'])->name('user-create');
    Route::post('/user/create', [UserController::class, 'store'])->name('user-store');
    Route::get('/user/edit/{user}', [UserController::class, 'edit'])->name('user-edit');
    Route::patch('/user/edit/{user}', [UserController::class, 'update'])->name('user-update');
    Route::get('/user-list', [UserController::class, 'index'])->name('user-list');
    Route::delete('/user/{user}/delete', [UserController::class, 'destroy'])->name('user-delete');
    Route::get('/contacts', [PersonController::class, 'index'])->name('contacts-index');
    Route::delete('/contacts', [PersonController::class, 'destroy'])->name('contacts-delete');
    //Cancel assistance route
    Route::delete('/edition/{edition}/attendee/{attendee}', [FormController::class, 'cancel_attendee'])->name('cancel-attendee-edition');
    //Tickets route
    Route::get('edition/{edition}/attendee/{attendee}/ticket', [FormController::class, 'downloadTicket'])->name('ticket-download');
    //Portfolios route
    Route::get('portfolios-list', [ClientPortfolioController::class, 'list'])->name('portfolios-list');
});

Route::middleware(['auth', 'doorman'])->group(function () {
    Route::get('/checkin', [CheckInController::class, 'create'])->name('checkin');
    Route::post('/checkin', [CheckInController::class, 'store'])->name('checkin-store');
});

Route::middleware(['auth'])->group(function (){
    Route::get('/dashboard', [AuthController::class, 'create'])->name('dashboard');
    Route::post('/logout', [AuthController::class, 'destroy'])->name('logout');
    Route::get('/events/index', [EventController::class, 'index'])->name('events-index');
    Route::get('/events/show/{event}', [EventController::class, 'show'])->name('events-show');
    Route::get('/event/{event}/edit', [EventController::class, 'edit'])->name('events-edit');
    Route::patch('event/{event}/edit', [EventController::class, 'update'])->name('events-update');
    Route::post('/event/{event}/assign-doorman', [EventController::class, 'assignDoorman'])->name('assign-doorman');
    Route::get('/event/{event}/edition', [EditionController::class, 'create'])->name('editions-create');
    Route::post('/event/{event}/edition', [EditionController::class, 'store'])->name('editions-store');
    Route::get('/edition/{edition}', [EditionController::class, 'edit'])->name('editions-edit');
    Route::patch('/edition/{edition}', [EditionController::class, 'update'])->name('editions-update');
    Route::get('/edition/{edition}/guest-list/create', [GuestListController::class, 'create'])->name('guest-list-create');
    Route::get('/user/{id}/portfolio', [ClientPortfolioController::class, 'index'])->name('portfolios-index');
    Route::get('/portfolio/{portfolio}', [ClientPortfolioController::class, 'show'])->name('portfolios-show');
    Route::get('/user/{user}/editions', [EditionController::class, 'managerEditions'])->name('manager-editions');
    //Invitations route
    Route::get('/edition/{edition}/invitations', [InvitationListController::class, 'create'])->name('edition-invitation-list');
    Route::post('/edition/{edition}/invitations', [InvitationListController::class, 'store'])->name('invitation-list-store');
    Route::get('/user/{id}/invitation-list', [InvitationListController::class, 'index'])->name('invitation-lists-index');
    Route::get('/invitation-list/{list}', [InvitationListController::class, 'show'])->name('invitation-list-show');
    Route::patch('/invitation-list/{list}', [InvitationListController::class, 'update'])->name('invitation-list-update');
    Route::post('/invitation-list/{list}/send', [InvitationListController::class, 'sendInvitations'])->name('invitation-list-send');
    //Supervised invitations routes
    Route::get('/invitations-list/{user}', [InvitationListController::class, 'supervisedIndex'])->name('supervised-invitations-list');
    Route::patch('/edition/{edition}/manager/{manager}/invitations-capacity', [InvitationListController::class, 'updateManagerCapacity'])->name('supervised-manager-capacity-update');
});

