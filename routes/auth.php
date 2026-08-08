<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\PasswordCodeController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rutas de Autenticación Oficiales de Laravel
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login.post');

    // Flujo de Recuperación por Código OTP (Email -> Código 6 dígitos -> Nueva Contraseña)
    Route::get('/recuperar-contrasena', [PasswordCodeController::class, 'showEmailForm'])->name('password.code.email');
    Route::post('/recuperar-contrasena/enviar-codigo', [PasswordCodeController::class, 'sendCode'])->name('password.code.send');
    Route::get('/recuperar-contrasena/verificar-codigo', [PasswordCodeController::class, 'showVerifyCode'])->name('password.code.verify');
    Route::post('/recuperar-contrasena/verificar-codigo', [PasswordCodeController::class, 'verifyCode'])->name('password.code.check');
    Route::post('/recuperar-contrasena/reenviar-codigo', [PasswordCodeController::class, 'resendCode'])->name('password.code.resend');
    Route::get('/recuperar-contrasena/nueva-clave', [PasswordCodeController::class, 'showResetForm'])->name('password.code.reset');
    Route::post('/recuperar-contrasena/guardar-clave', [PasswordCodeController::class, 'updatePassword'])->name('password.code.update');

    // Alias de compatibilidad
    Route::get('/forgot-password', fn() => redirect()->route('password.code.email'))->name('password.request');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});
