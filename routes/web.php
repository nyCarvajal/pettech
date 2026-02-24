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
use App\Http\Controllers\PosInvoiceController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
        if (blank(DB::connection('tenant')->getDatabaseName()) && ($user = Auth::user()) && filled($user->db)) {
            config(['database.connections.tenant.database' => $user->db]);
            DB::purge('tenant');
            DB::reconnect('tenant');
        }

        if (blank(DB::connection('tenant')->getDatabaseName())) {
            $lowStockCount = 0;
            return view('dashboard', compact('lowStockCount'));
        }

        $lowStockCount = \App\Models\Product::on('tenant')
            ->where('is_service', false)
            ->with('stocks')
            ->get()
            ->filter(fn (\App\Models\Product $product) => $product->stocks->sum('qty') <= $product->min_stock)
            ->count();

        return view('dashboard', compact('lowStockCount'));
    })->name('dashboard');

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
