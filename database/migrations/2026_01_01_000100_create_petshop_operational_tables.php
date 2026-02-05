<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_catalog', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('name');
            $table->string('service_type', 30); // bano, grooming, consulta, venta
            $table->text('description')->nullable();
            $table->decimal('base_price', 12, 2)->default(0);
            $table->unsignedInteger('estimated_minutes')->default(30);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
            $table->index(['tenant_id', 'service_type']);
            $table->unique(['tenant_id', 'name']);
        });

        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('client_id')->constrained('clients')->restrictOnDelete();
            $table->foreignId('pet_id')->constrained('pets')->restrictOnDelete();
            $table->foreignId('service_id')->constrained('service_catalog')->restrictOnDelete();
            $table->foreignId('groomer_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('scheduled_start');
            $table->dateTime('scheduled_end')->nullable();
            $table->string('status', 25)->default('pending');
            $table->string('channel', 20)->default('internal');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['tenant_id', 'scheduled_start']);
            $table->index(['tenant_id', 'status']);
            $table->index(['tenant_id', 'groomer_user_id', 'scheduled_start'], 'appointments_groomer_date_idx');
        });

        Schema::create('appointment_status_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('appointment_id')->constrained('appointments')->cascadeOnDelete();
            $table->string('from_status', 25)->nullable();
            $table->string('to_status', 25);
            $table->text('comment')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['tenant_id', 'appointment_id']);
        });

        Schema::create('warehouses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('name');
            $table->string('code', 40);
            $table->string('location')->nullable();
            $table->boolean('is_main')->default(false);
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['tenant_id', 'code']);
        });

        Schema::create('product_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('name');
            $table->string('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['tenant_id', 'name']);
        });

        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('category_id')->nullable()->constrained('product_categories')->nullOnDelete();
            $table->string('sku', 60);
            $table->string('name');
            $table->string('unit', 20)->default('unidad');
            $table->decimal('cost_price', 12, 2)->default(0);
            $table->decimal('sale_price', 12, 2)->default(0);
            $table->unsignedInteger('min_stock')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['tenant_id', 'sku']);
            $table->index(['tenant_id', 'name']);
        });

        Schema::create('inventory_stocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('warehouse_id')->constrained('warehouses')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->decimal('stock', 12, 2)->default(0);
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['tenant_id', 'warehouse_id', 'product_id'], 'inventory_stocks_unique_row');
        });

        Schema::create('inventory_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('warehouse_id')->constrained('warehouses')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->string('movement_type', 20); // in, out, adjust, transfer
            $table->decimal('quantity', 12, 2);
            $table->decimal('unit_cost', 12, 2)->nullable();
            $table->string('reference_type', 30)->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['tenant_id', 'movement_type']);
            $table->index(['tenant_id', 'product_id', 'created_at']);
        });

        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('client_id')->nullable()->constrained('clients')->nullOnDelete();
            $table->foreignId('appointment_id')->nullable()->constrained('appointments')->nullOnDelete();
            $table->string('invoice_number', 40);
            $table->string('invoice_type', 20)->default('pos'); // pos/interna
            $table->string('status', 25)->default('draft');
            $table->dateTime('issued_at')->nullable();
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('tax_total', 12, 2)->default(0);
            $table->decimal('discount_total', 12, 2)->default(0);
            $table->decimal('grand_total', 12, 2)->default(0);
            $table->string('currency', 3)->default('COP');
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['tenant_id', 'invoice_number']);
            $table->index(['tenant_id', 'status']);
            $table->index(['tenant_id', 'issued_at']);
        });

        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('invoice_id')->constrained('invoices')->cascadeOnDelete();
            $table->string('item_type', 20); // product/service
            $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete();
            $table->foreignId('service_id')->nullable()->constrained('service_catalog')->nullOnDelete();
            $table->string('description');
            $table->decimal('quantity', 12, 2);
            $table->decimal('unit_price', 12, 2);
            $table->decimal('tax_rate', 5, 2)->default(0);
            $table->decimal('discount_rate', 5, 2)->default(0);
            $table->decimal('line_total', 12, 2);
            $table->timestamps();
            $table->softDeletes();
            $table->index(['tenant_id', 'invoice_id']);
        });

        Schema::create('invoice_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('invoice_id')->constrained('invoices')->cascadeOnDelete();
            $table->string('payment_method', 20);
            $table->decimal('amount', 12, 2);
            $table->dateTime('paid_at');
            $table->string('reference')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['tenant_id', 'invoice_id']);
        });

        Schema::create('dian_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('invoice_id')->constrained('invoices')->cascadeOnDelete();
            $table->string('environment', 20)->default('habilitacion');
            $table->string('document_type', 20)->default('factura_venta');
            $table->string('cufe')->nullable();
            $table->string('xml_path')->nullable();
            $table->string('zip_path')->nullable();
            $table->string('qr_data')->nullable();
            $table->string('dian_status', 25)->default('draft');
            $table->string('validation_code')->nullable();
            $table->text('status_message')->nullable();
            $table->dateTime('sent_at')->nullable();
            $table->dateTime('validated_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['tenant_id', 'invoice_id']);
            $table->index(['tenant_id', 'dian_status']);
            $table->index(['tenant_id', 'cufe']);
        });

        Schema::create('dian_document_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('dian_document_id')->constrained('dian_documents')->cascadeOnDelete();
            $table->string('event', 40);
            $table->text('payload')->nullable();
            $table->timestamp('event_at')->useCurrent();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['tenant_id', 'dian_document_id']);
        });

        Schema::create('dashboard_snapshots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('dashboard_type', 30); // admin/groomer
            $table->date('snapshot_date');
            $table->json('kpis_json');
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['tenant_id', 'dashboard_type', 'snapshot_date'], 'dashboard_snapshot_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dashboard_snapshots');
        Schema::dropIfExists('dian_document_events');
        Schema::dropIfExists('dian_documents');
        Schema::dropIfExists('invoice_payments');
        Schema::dropIfExists('invoice_items');
        Schema::dropIfExists('invoices');
        Schema::dropIfExists('inventory_movements');
        Schema::dropIfExists('inventory_stocks');
        Schema::dropIfExists('products');
        Schema::dropIfExists('product_categories');
        Schema::dropIfExists('warehouses');
        Schema::dropIfExists('appointment_status_logs');
        Schema::dropIfExists('appointments');
        Schema::dropIfExists('service_catalog');
    }
};
