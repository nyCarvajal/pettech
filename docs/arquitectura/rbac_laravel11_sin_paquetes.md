# RBAC Laravel 11 sin paquetes externos

## Componentes implementados
- **Tablas RBAC**: `roles`, `permissions`, `user_role`, `role_permission`.
- **Middleware**:
  - `role:xxx` → `App\Http\Middleware\RoleMiddleware`
  - `permission:xxx` → `App\Http\Middleware\PermissionMiddleware`
- **Policies**:
  - `AppointmentPolicy` (Citas)
  - `InvoicePolicy` (Facturas)
  - `InventoryMovementPolicy` (Inventario)
  - `GroomingPolicy` (Grooming vía Gate `grooming.execute`)
- **FormRequest**:
  - `StoreRoleRequest`, `UpdateRoleRequest`
  - `StorePermissionRequest`, `UpdatePermissionRequest`
  - `StoreUserRequest`, `UpdateUserRequest`
- **CRUD Blade + Tailwind**:
  - Roles: `resources/views/roles/*`
  - Permisos: `resources/views/permissions/*`
  - Asignación de roles a usuarios: `resources/views/users/create.blade.php` y `resources/views/users/edit.blade.php`
  - Asignación de permisos a roles (checklist): `resources/views/roles/create.blade.php` y `resources/views/roles/edit.blade.php`

## Estructura de carpetas
- `app/Http/Controllers/{RoleController,PermissionController,UserController}.php`
- `app/Http/Middleware/{RoleMiddleware,PermissionMiddleware}.php`
- `app/Http/Requests/*`
- `app/Policies/*`
- `app/Models/{User,Role,Permission}.php`
- `database/migrations/2025_12_01_000000_create_users_roles_permissions_tables.php (+ 2026_01_01_000000_add_tenant_audit_columns_to_core_tables.php)`
- `routes/web.php` (ejemplos `middleware` y `can`)

## Ejemplo de rutas protegidas
```php
Route::middleware('permission:manage roles')->group(function () {
    Route::resource('roles', RoleController::class);
});

Route::get('/policy-demo/citas', fn () => 'Policy Citas OK')
    ->middleware('can:viewAny,' . \App\Models\Appointment::class);
```
