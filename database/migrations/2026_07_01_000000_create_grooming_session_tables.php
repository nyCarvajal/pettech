<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grooming_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('appointment_id')->constrained('appointments')->cascadeOnDelete();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('groomer_user_id')->constrained('users')->cascadeOnDelete();
            $table->string('current_stage', 30)->default('received');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->timestamps();

            $table->unique('appointment_id');
            $table->index(['tenant_id', 'groomer_user_id', 'current_stage'], 'grooming_sessions_dashboard_idx');
        });

        Schema::create('grooming_stage_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('grooming_session_id')->constrained('grooming_sessions')->cascadeOnDelete();
            $table->string('stage', 30);
            $table->timestamp('changed_at');
            $table->foreignId('changed_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->index(['grooming_session_id', 'changed_at'], 'grooming_stage_logs_session_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grooming_stage_logs');
        Schema::dropIfExists('grooming_sessions');
    }
};
