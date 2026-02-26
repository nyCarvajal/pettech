<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('pet_customer', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('pet_id')->constrained('pets')->cascadeOnDelete();
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();
            $table->enum('relationship', ['owner', 'other'])->default('owner');
            $table->boolean('is_primary')->default(false);
            $table->foreignId('created_by')->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['pet_id', 'customer_id']);
            $table->index(['tenant_id', 'pet_id']);
            $table->index(['tenant_id', 'customer_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pet_customer');
    }
};
