# Diseño SaaS PetShop / Peluquería (Laravel 11)

## Enfoque multi-tenant sin paquetes
- Todas las entidades operativas incluyen `tenant_id` y `created_by`.
- Patrón recomendado en app: `TenantContext` + `GlobalScope` por `tenant_id` en modelos críticos.
- Todas las consultas de módulos deben filtrar por tenant activo.

## Módulos
1. **RBAC**: `roles`, `permissions`, `user_role`, `role_permission`.
2. **CRM clínico**: `clients`, `pets`.
3. **Agenda**: `service_catalog`, `appointments`, `appointment_status_logs`.
4. **Inventario**: `product_categories`, `products`, `warehouses`, `inventory_stocks`, `inventory_movements`.
5. **Facturación POS + interna**: `invoices`, `invoice_items`, `invoice_payments`.
6. **DIAN**: `dian_documents`, `dian_document_events`.
7. **Dashboard**: `dashboard_snapshots` (KPIs precalculados por perfil `admin/groomer`).

## Listado de tablas y campos

### Seguridad y usuarios
- `users`: tenant_id, created_by, name, email, password, is_active, timestamps, deleted_at.
- `roles`: tenant_id, created_by, name, guard_name, description, timestamps, deleted_at.
- `permissions`: tenant_id, created_by, name, guard_name, description, timestamps, deleted_at.
- `user_role`: tenant_id, created_by, role_id, model_id, model_type, timestamps, deleted_at.
- `role_permission`: tenant_id, created_by, permission_id, role_id, timestamps, deleted_at.

### Clientes y mascotas
- `clients`: tenant_id, created_by, name, phone, email, document, address, notes, active, timestamps, deleted_at.
- `pets`: tenant_id, created_by, client_id, name, species, breed, size, birthdate, sex, color, allergies, behavior_notes, grooming_preferences, active, timestamps, deleted_at.

### Agenda
- `service_catalog`: tenant_id, created_by, name, service_type, description, base_price, estimated_minutes, is_active, timestamps, deleted_at.
- `appointments`: tenant_id, created_by, client_id, pet_id, service_id, groomer_user_id, scheduled_start, scheduled_end, status, channel, notes, timestamps, deleted_at.
- `appointment_status_logs`: tenant_id, created_by, appointment_id, from_status, to_status, comment, timestamps, deleted_at.

### Inventario
- `product_categories`: tenant_id, created_by, name, description, timestamps, deleted_at.
- `products`: tenant_id, created_by, category_id, sku, name, unit, cost_price, sale_price, min_stock, is_active, timestamps, deleted_at.
- `warehouses`: tenant_id, created_by, name, code, location, is_main, timestamps, deleted_at.
- `inventory_stocks`: tenant_id, created_by, warehouse_id, product_id, stock, timestamps, deleted_at.
- `inventory_movements`: tenant_id, created_by, warehouse_id, product_id, movement_type, quantity, unit_cost, reference_type, reference_id, notes, timestamps, deleted_at.

### Facturación + DIAN
- `invoices`: tenant_id, created_by, client_id, appointment_id, invoice_number, invoice_type, status, issued_at, subtotal, tax_total, discount_total, grand_total, currency, timestamps, deleted_at.
- `invoice_items`: tenant_id, created_by, invoice_id, item_type, product_id, service_id, description, quantity, unit_price, tax_rate, discount_rate, line_total, timestamps, deleted_at.
- `invoice_payments`: tenant_id, created_by, invoice_id, payment_method, amount, paid_at, reference, timestamps, deleted_at.
- `dian_documents`: tenant_id, created_by, invoice_id, environment, document_type, cufe, xml_path, zip_path, qr_data, dian_status, validation_code, status_message, sent_at, validated_at, timestamps, deleted_at.
- `dian_document_events`: tenant_id, created_by, dian_document_id, event, payload, event_at, timestamps, deleted_at.

### Dashboard
- `dashboard_snapshots`: tenant_id, created_by, dashboard_type, snapshot_date, kpis_json, timestamps, deleted_at.

## Relaciones Eloquent clave
- `Tenant hasMany User/Client/Pet/Appointment/Product/Invoice/...`
- `User belongsTo Tenant`, `User belongsToMany Role`.
- `Role belongsToMany Permission`.
- `Client hasMany Pet`, `Client hasMany Appointment`, `Client hasMany Invoice`.
- `Pet belongsTo Client`, `Pet hasMany Appointment`.
- `Appointment belongsTo Client/Pet/ServiceCatalog/Groomer(User)`.
- `Warehouse hasMany InventoryStock/InventoryMovement`.
- `Product belongsTo ProductCategory`, `Product hasMany InventoryStock/InventoryMovement`.
- `Invoice belongsTo Client/Appointment`, `Invoice hasMany InvoiceItem/InvoicePayment`, `Invoice hasOne DianDocument`.
- `DianDocument hasMany DianDocumentEvent`.

## Índices relevantes
- Búsqueda por tenant y negocio: `(tenant_id, name|status|fecha)`.
- Integridad de catálogos: únicos `(tenant_id, sku|code|invoice_number|name)`.
- Alta concurrencia agenda: `(tenant_id, groomer_user_id, scheduled_start)`.
- DIAN: `(tenant_id, dian_status)`, `(tenant_id, cufe)`.

## Estados recomendados
- `appointments.status`: pending, confirmed, in_progress, done, canceled, no_show.
- `invoices.status`: draft, issued, paid, partially_paid, void.
- `dian_documents.dian_status`: draft, queued, sent, accepted, rejected, contingency.

## Seed base RBAC
- Roles: **Admin**, **Recepción**, **Peluquero**.
- Permisos por módulo: dashboard, clientes, mascotas, agenda, inventario, facturación + DIAN.
- Seeder: `database/seeders/RbacSeeder.php`.
