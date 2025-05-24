<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Events\EventController;
use App\Http\Controllers\Events\EventTypeController;
use App\Http\Controllers\Location\LocationController;
use App\Http\Controllers\Reservations\ReservationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;




Route::post('login', [AuthController::class, 'login'])->name('login');

Route::middleware(['auth:api'])->group(function () {


    Route::post('logout', [AuthController::class, 'logout'])->name('logout');


    Route::middleware(['role:adminRole|userRole'])->group(function () {
        // Event Types 
        Route::get('show/event/type/{id}', [EventTypeController::class, 'show'])->name('show.eventType');
        Route::get('get/all/event/types', [EventTypeController::class, 'index'])->name('all.eventType');

        // Locations 
        Route::get('show/location/{id}', [LocationController::class, 'show'])->name('location.show');
        Route::get('get/all/location', [LocationController::class, 'index'])->name('location.all');

        // Events 
        Route::get('get/event/{id}', [EventController::class, 'show'])->name('event.show');
        Route::get('get/all/events', [EventController::class, 'index'])->name('event.all');
        //reservation

        Route::get('reservations', [ReservationController::class, 'index'])->name('reservations.index');

        Route::get('reservation/{id}', [ReservationController::class, 'show'])->name('reservations.show');

        Route::post('reservation', [ReservationController::class, 'store'])->name('reservations.store');

        Route::post('reservation/{id}', [ReservationController::class, 'update'])->name('reservations.update');

        Route::delete('reservation/{id}', [ReservationController::class, 'destroy'])->name('reservations.destroy');
    });


    Route::middleware(['role:adminRole'])->group(function () {
        // Users 
        Route::post('register', [AuthController::class, 'registerUser'])->name('register');
        Route::delete('delete/user/{id}', [AuthController::class, 'destroy'])->name('user.delete');
        // Event Types 
        Route::post('make/event/type', [EventTypeController::class, 'store'])->name('make.eventType');
        Route::post('update/event/type/{id}', [EventTypeController::class, 'update'])->name('update.eventType');
        Route::delete('delete/event/type/{id}', [EventTypeController::class, 'destroy'])->name('delete.eventType');

        // Locations 
        Route::post('make/location', [LocationController::class, 'store'])->name('location.make');
        Route::post('update/location/{id}', [LocationController::class, 'update'])->name('location.update');
        Route::delete('delete/location/{id}', [LocationController::class, 'destroy'])->name('delete.location');

        // Events 
        Route::post('store/event', [EventController::class, 'store'])->name('event.store');
        Route::post('update/event/{id}', [EventController::class, 'update'])->name('event.update');
        Route::delete('delete/event/{id}', [EventController::class, 'destroy'])->name('event.delete');
    });
});
