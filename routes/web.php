<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\StockMovementController;
use App\Http\Controllers\WarehouseController;
use App\Http\Controllers\PetController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
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
    Route::get('/dashboard', function () {
        $lowStockCount = \App\Models\Product::on('tenant')
            ->query()
            ->where('is_service', false)
            ->with('stocks')
            ->get()
            ->filter(fn (\App\Models\Product $product) => $product->stocks->sum('qty') <= $product->min_stock)
            ->count();

        return view('dashboard', compact('lowStockCount'));
    })->name('dashboard');

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

    Route::resource('appointments', AppointmentController::class)->except(['show']);

    Route::resource('categories', CategoryController::class)->except(['show']);
    Route::resource('warehouses', WarehouseController::class)->except(['show']);
    Route::resource('products', ProductController::class)->except(['show']);
    Route::get('products/{product}/kardex', [ProductController::class, 'kardex'])->name('products.kardex');

    Route::get('stock/movements', [StockMovementController::class, 'create'])->name('stock.movements.create');
    Route::post('stock/movements', [StockMovementController::class, 'store'])->name('stock.movements.store');
    Route::get('stock/alerts/low', [StockMovementController::class, 'lowStock'])->name('stock.low');
    Route::post('appointments/{appointment}/confirm', [AppointmentController::class, 'confirm'])->name('appointments.confirm');
    Route::post('appointments/{appointment}/cancel', [AppointmentController::class, 'cancel'])->name('appointments.cancel');
});
