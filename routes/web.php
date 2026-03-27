<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

Route::get('/', function () {
    return view('welcome');
});

// Authentication routes (built-in Laravel)
Route::middleware('auth')->group(function () {
    // User resource routes (CRUD)
    Route::resource('users', UserController::class);

    // Additional user actions
  
});

// Built-in Laravel authentication scaffolding
require __DIR__.'/auth.php';
