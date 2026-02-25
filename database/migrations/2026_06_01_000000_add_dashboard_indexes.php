<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->index(['tenant_id', 'issued_at', 'status'], 'invoices_tenant_issued_status_idx');
        });

        Schema::table('invoice_items', function (Blueprint $table) {
            $table->index(['invoice_id', 'item_type'], 'invoice_items_invoice_item_type_idx');
            $table->index(['tenant_id', 'product_id', 'service_id'], 'invoice_items_tenant_item_refs_idx');
        });

        Schema::table('appointments', function (Blueprint $table) {
            $table->index(['tenant_id', 'start_at', 'status'], 'appointments_tenant_start_status_idx');
            $table->index(['tenant_id', 'scheduled_start', 'status'], 'appointments_tenant_scheduled_status_idx');
        });

        Schema::table('inventory_stocks', function (Blueprint $table) {
            $table->index(['tenant_id', 'product_id'], 'inventory_stocks_tenant_product_idx');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->index(['tenant_id', 'is_service', 'is_active'], 'products_tenant_service_active_idx');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex('products_tenant_service_active_idx');
        });

        Schema::table('inventory_stocks', function (Blueprint $table) {
            $table->dropIndex('inventory_stocks_tenant_product_idx');
        });

        Schema::table('appointments', function (Blueprint $table) {
            $table->dropIndex('appointments_tenant_scheduled_status_idx');
            $table->dropIndex('appointments_tenant_start_status_idx');
        });

        Schema::table('invoice_items', function (Blueprint $table) {
            $table->dropIndex('invoice_items_tenant_item_refs_idx');
            $table->dropIndex('invoice_items_invoice_item_type_idx');
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->dropIndex('invoices_tenant_issued_status_idx');
        });
    }
};
