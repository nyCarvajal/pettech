<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('pets', function (Blueprint $table) {
            if (! Schema::hasColumn('pets', 'notes')) {
                $table->text('notes')->nullable()->after('color');
            }
        });

        if (Schema::hasColumn('pets', 'client_id')) {
            DB::statement('ALTER TABLE pets MODIFY client_id BIGINT UNSIGNED NULL');
        }

        Schema::table('pets', function (Blueprint $table) {
            $table->index(['tenant_id', 'name']);
            $table->index(['tenant_id', 'species']);
        });
    }

    public function down(): void
    {
        Schema::table('pets', function (Blueprint $table) {
            if (Schema::hasColumn('pets', 'notes')) {
                $table->dropColumn('notes');
            }

            $table->dropIndex(['tenant_id', 'name']);
            $table->dropIndex(['tenant_id', 'species']);
        });

        if (Schema::hasColumn('pets', 'client_id')) {
            DB::statement('ALTER TABLE pets MODIFY client_id BIGINT UNSIGNED NOT NULL');
        }
    }
};
