<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            if (!Schema::hasColumn('invoices', 'number')) {
                $table->string('number', 40)->nullable()->after('invoice_number');
                $table->unique(['tenant_id', 'number']);
            }

            if (!Schema::hasColumn('invoices', 'customer_id')) {
                $table->foreignId('customer_id')->nullable()->after('client_id')->constrained('clients')->nullOnDelete();
            }

            if (!Schema::hasColumn('invoices', 'pet_id')) {
                $table->foreignId('pet_id')->nullable()->after('customer_id')->constrained('pets')->nullOnDelete();
            }

            if (!Schema::hasColumn('invoices', 'notes')) {
                $table->text('notes')->nullable()->after('tax_total');
            }

            if (!Schema::hasColumn('invoices', 'total')) {
                $table->decimal('total', 12, 2)->default(0)->after('tax_total');
            }
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            if (Schema::hasColumn('invoices', 'pet_id')) {
                $table->dropForeign(['pet_id']);
                $table->dropColumn('pet_id');
            }

            if (Schema::hasColumn('invoices', 'customer_id')) {
                $table->dropForeign(['customer_id']);
                $table->dropColumn('customer_id');
            }

            if (Schema::hasColumn('invoices', 'number')) {
                $table->dropUnique(['tenant_id', 'number']);
                $table->dropColumn('number');
            }

            if (Schema::hasColumn('invoices', 'notes')) {
                $table->dropColumn('notes');
            }

            if (Schema::hasColumn('invoices', 'total')) {
                $table->dropColumn('total');
            }
        });
    }
};
