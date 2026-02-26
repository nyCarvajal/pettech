<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            if (Schema::hasColumn('appointments', 'client_id')) {
                $table->dropConstrainedForeignId('client_id');
            }

            if (Schema::hasColumn('appointments', 'service_id')) {
                $table->dropConstrainedForeignId('service_id');
            }

            if (Schema::hasColumn('appointments', 'groomer_user_id')) {
                $table->dropConstrainedForeignId('groomer_user_id');
            }

            if (Schema::hasColumn('appointments', 'scheduled_start')) {
                $table->dropColumn('scheduled_start');
            }

            if (Schema::hasColumn('appointments', 'scheduled_end')) {
                $table->dropColumn('scheduled_end');
            }

            if (Schema::hasColumn('appointments', 'channel')) {
                $table->dropColumn('channel');
            }

            $table->enum('service_type', ['grooming', 'consult', 'sale', 'other'])->default('other')->change();
            $table->enum('status', ['scheduled', 'confirmed', 'in_progress', 'done', 'cancelled', 'no_show'])->default('scheduled')->change();

            $table->index(['tenant_id', 'start_at', 'status'], 'appointments_tenant_date_status_idx');
        });
    }

    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropIndex('appointments_tenant_date_status_idx');
        });
    }
};
