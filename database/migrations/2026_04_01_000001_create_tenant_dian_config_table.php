<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tenant_dian_config', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('software_id');
            $table->string('pin');
            $table->string('certificate_path');
            $table->string('certificate_password');
            $table->string('environment', 10)->default('test');
            $table->string('resolution_number');
            $table->string('prefix', 20);
            $table->unsignedBigInteger('range_from');
            $table->unsignedBigInteger('range_to');
            $table->timestamps();

            $table->unique('tenant_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tenant_dian_config');
    }
};
