<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\PetController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Models\Appointment;
use App\Models\InventoryMovement;
use App\Models\Invoice;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('dashboard'));

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);

    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store']);
});

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

Route::middleware(['auth'])->group(function () {
    Route::view('/dashboard', 'dashboard')->name('dashboard');

    // RBAC middleware por permiso/rol
    Route::middleware('permission:manage users')->group(function () {
        Route::resource('users', UserController::class);
        Route::patch('users/{user}/toggle', [UserController::class, 'toggleStatus'])->name('users.toggle');
    });

    Route::middleware('permission:manage roles')->group(function () {
        Route::resource('roles', RoleController::class);
    });

    Route::middleware('permission:manage permissions')->group(function () {
        Route::resource('permissions', PermissionController::class);
    });

    Route::middleware('permission:manage clients')->group(function () {
        Route::resource('clients', ClientController::class);
        Route::resource('clients.pets', PetController::class)->shallow()->except(['index']);
    });

    // Ejemplos de uso de Policies (can)
    Route::get('/policy-demo/citas', fn () => 'Policy Citas OK')
        ->middleware('can:viewAny,' . Appointment::class);

    Route::get('/policy-demo/facturas', fn () => 'Policy Facturas OK')
        ->middleware('can:viewAny,' . Invoice::class);

    Route::get('/policy-demo/inventario', fn () => 'Policy Inventario OK')
        ->middleware('can:viewAny,' . InventoryMovement::class);

    Route::get('/policy-demo/grooming/{appointment}', fn (Appointment $appointment) => 'Policy Grooming OK')
        ->middleware('can:grooming.execute,appointment');
});
