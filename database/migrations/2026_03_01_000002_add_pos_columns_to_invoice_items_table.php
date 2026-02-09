<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoice_items', function (Blueprint $table) {
            if (!Schema::hasColumn('invoice_items', 'qty')) {
                $table->decimal('qty', 12, 2)->nullable()->after('description');
            }

            if (!Schema::hasColumn('invoice_items', 'is_service')) {
                $table->boolean('is_service')->default(false)->after('line_total');
            }
        });
    }

    public function down(): void
    {
        Schema::table('invoice_items', function (Blueprint $table) {
            if (Schema::hasColumn('invoice_items', 'is_service')) {
                $table->dropColumn('is_service');
            }

            if (Schema::hasColumn('invoice_items', 'qty')) {
                $table->dropColumn('qty');
            }
        });
    }
};
