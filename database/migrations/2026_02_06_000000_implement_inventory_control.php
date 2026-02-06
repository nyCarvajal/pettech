<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('categories')) {
            Schema::create('categories', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->timestamps();
                $table->softDeletes();
                $table->unique('name');
            });
        }

        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'tax_rate')) {
                $table->decimal('tax_rate', 5, 2)->default(0)->after('cost_price');
            }
            if (!Schema::hasColumn('products', 'is_service')) {
                $table->boolean('is_service')->default(false)->after('tax_rate');
            }

            if (Schema::hasColumn('products', 'category_id')) {
                $table->dropConstrainedForeignId('category_id');
                $table->foreignId('category_id')->nullable()->after('created_by')->constrained('categories')->nullOnDelete();
            }
        });

        if (!Schema::hasTable('stock')) {
            Schema::create('stock', function (Blueprint $table) {
                $table->id();
                $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
                $table->foreignId('warehouse_id')->constrained('warehouses')->cascadeOnDelete();
                $table->decimal('qty', 12, 2)->default(0);
                $table->timestamps();
                $table->unique(['product_id', 'warehouse_id']);
            });
        }

        if (!Schema::hasTable('stock_movements')) {
            Schema::create('stock_movements', function (Blueprint $table) {
                $table->id();
                $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
                $table->foreignId('warehouse_id')->constrained('warehouses')->cascadeOnDelete();
                $table->enum('movement_type', ['in', 'out', 'adjustment']);
                $table->decimal('qty', 12, 2);
                $table->string('reason')->nullable();
                $table->string('reference_type')->nullable();
                $table->unsignedBigInteger('reference_id')->nullable();
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
                $table->index(['product_id', 'warehouse_id', 'created_at']);
            });
        }

        Schema::table('invoices', function (Blueprint $table) {
            if (!Schema::hasColumn('invoices', 'inventory_applied_at')) {
                $table->timestamp('inventory_applied_at')->nullable()->after('issued_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            if (Schema::hasColumn('invoices', 'inventory_applied_at')) {
                $table->dropColumn('inventory_applied_at');
            }
        });

        Schema::dropIfExists('stock_movements');
        Schema::dropIfExists('stock');

        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'is_service')) {
                $table->dropColumn('is_service');
            }
            if (Schema::hasColumn('products', 'tax_rate')) {
                $table->dropColumn('tax_rate');
            }
        });

        Schema::dropIfExists('categories');
    }
};
