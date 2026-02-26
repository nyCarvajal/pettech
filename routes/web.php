<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\DashboardAdminController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ElectronicInvoiceController;
use App\Http\Controllers\Groomer\GroomerDashboardController;
use App\Http\Controllers\TenantDianConfigController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\StockMovementController;
use App\Http\Controllers\WarehouseController;
use App\Http\Controllers\PetController;
use App\Http\Controllers\Patient\PetPatientController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\PosInvoiceController;
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
    Route::get('/dashboard', [DashboardAdminController::class, 'index'])->name('dashboard');

    Route::middleware('role_or_permission:Admin|manage users')->group(function () {
        Route::resource('users', UserController::class);
        Route::patch('users/{user}/toggle', [UserController::class, 'toggleStatus'])->name('users.toggle');
    });

    Route::middleware('role_or_permission:Admin|manage roles')->group(function () {
        Route::resource('roles', RoleController::class);
    });

    Route::middleware('role_or_permission:Admin|manage permissions')->group(function () {
        Route::resource('permissions', PermissionController::class);
    });

    Route::middleware('role_or_permission:Admin|manage clients')->group(function () {
        Route::resource('clients', ClientController::class);
        Route::resource('clients.pets', PetController::class)->shallow()->except(['index']);
    });

    Route::middleware('role_or_permission:Admin|manage clients')->group(function () {
        Route::resource('patient-pets', PetPatientController::class);
    });

    Route::get('appointments/day', [AppointmentController::class, 'day'])->name('appointments.day');
    Route::get('appointments/week', [AppointmentController::class, 'week'])->name('appointments.week');
    Route::resource('appointments', AppointmentController::class)->except(['show']);

    Route::prefix('groomer/dashboard')->name('groomer.dashboard.')->group(function () {
        Route::get('/', [GroomerDashboardController::class, 'index'])->name('index');
        Route::patch('sessions/{groomingSession}/stage/advance', [GroomerDashboardController::class, 'advance'])->name('stage.advance');
        Route::patch('sessions/{groomingSession}/stage/rollback', [GroomerDashboardController::class, 'rollback'])->name('stage.rollback');
    });

    Route::resource('categories', CategoryController::class)->except(['show']);
    Route::resource('warehouses', WarehouseController::class)->except(['show']);
    Route::resource('products', ProductController::class)->except(['show']);
    Route::get('products/{product}/kardex', [ProductController::class, 'kardex'])->name('products.kardex');

    Route::get('stock/movements', [StockMovementController::class, 'create'])->name('stock.movements.create');
    Route::post('stock/movements', [StockMovementController::class, 'store'])->name('stock.movements.store');
    Route::get('stock/alerts/low', [StockMovementController::class, 'lowStock'])->name('stock.low');
    Route::post('appointments/{appointment}/confirm', [AppointmentController::class, 'confirm'])->name('appointments.confirm');
    Route::post('appointments/{appointment}/start', [AppointmentController::class, 'start'])->name('appointments.start');
    Route::post('appointments/{appointment}/finish', [AppointmentController::class, 'finish'])->name('appointments.finish');
    Route::post('appointments/{appointment}/cancel', [AppointmentController::class, 'cancel'])->name('appointments.cancel');


    Route::prefix('dian')->name('dian.')->group(function () {
        Route::get('invoices', [ElectronicInvoiceController::class, 'index'])->name('invoices.index');
        Route::get('invoices/{invoice}', [ElectronicInvoiceController::class, 'show'])->name('invoices.show');
        Route::post('invoices/{invoice}/retry', [ElectronicInvoiceController::class, 'retry'])->name('invoices.retry');

        Route::get('config', [TenantDianConfigController::class, 'edit'])->name('config.edit');
        Route::put('config', [TenantDianConfigController::class, 'update'])->name('config.update');
    });

    Route::prefix('pos')->name('pos.')->group(function () {
        Route::get('invoices/create', [PosInvoiceController::class, 'create'])->name('invoices.create');
        Route::post('invoices', [PosInvoiceController::class, 'store'])->name('invoices.store');
        Route::get('invoices/{invoice}', [PosInvoiceController::class, 'show'])->name('invoices.show');
        Route::post('invoices/{invoice}/items', [PosInvoiceController::class, 'addItem'])->name('invoices.items.store');
        Route::post('invoices/{invoice}/payments', [PosInvoiceController::class, 'storePayment'])->name('invoices.payments.store');
        Route::post('invoices/{invoice}/issue', [PosInvoiceController::class, 'issue'])->name('invoices.issue');
        Route::get('invoices/{invoice}/print', [PosInvoiceController::class, 'print'])->name('invoices.print');
        Route::get('invoices/{invoice}/pdf', [PosInvoiceController::class, 'pdf'])->name('invoices.pdf');
    });
});
