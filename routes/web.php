<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

Route::get('/', function () {
    return view('welcome');
});

// User Management Routes (Protected)
Route::middleware('auth')->group(function () {
    // User resource routes (CRUD)
    Route::resource('users', UserController::class);

    // Additional user actions
    Route::post('/users/{user}/deactivate', [UserController::class, 'deactivate'])->name('users.deactivate');
    Route::post('/users/{user}/activate', [UserController::class, 'activate'])->name('users.activate');
    
    // Audit logs
    Route::get('/users/{user}/audit-logs', [UserController::class, 'auditLogs'])->name('users.audit-logs');
});

// Auth routes
require __DIR__.'/auth.php';
