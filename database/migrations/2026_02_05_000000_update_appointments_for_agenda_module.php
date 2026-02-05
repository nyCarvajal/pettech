<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            if (! Schema::hasColumn('appointments', 'code')) {
                $table->string('code', 50)->nullable()->after('tenant_id');
            }

            if (! Schema::hasColumn('appointments', 'customer_id')) {
                $table->foreignId('customer_id')->nullable()->after('code')->constrained('clients')->restrictOnDelete();
            }

            if (! Schema::hasColumn('appointments', 'service_type')) {
                $table->string('service_type', 20)->nullable()->after('pet_id');
            }

            if (! Schema::hasColumn('appointments', 'start_at')) {
                $table->dateTime('start_at')->nullable()->after('service_type');
            }

            if (! Schema::hasColumn('appointments', 'end_at')) {
                $table->dateTime('end_at')->nullable()->after('start_at');
            }

            if (! Schema::hasColumn('appointments', 'assigned_to_user_id')) {
                $table->foreignId('assigned_to_user_id')->nullable()->after('end_at')->constrained('users')->nullOnDelete();
            }

            $table->index(['tenant_id', 'status']);
            $table->index(['assigned_to_user_id', 'start_at', 'end_at'], 'appointments_assigned_schedule_idx');
        });
    }

    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            if (Schema::hasColumn('appointments', 'assigned_to_user_id')) {
                $table->dropConstrainedForeignId('assigned_to_user_id');
            }

            if (Schema::hasColumn('appointments', 'customer_id')) {
                $table->dropConstrainedForeignId('customer_id');
            }

            $table->dropIndex('appointments_assigned_schedule_idx');
            $table->dropColumn(['code', 'service_type', 'start_at', 'end_at']);
        });
    }
};
