<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('electronic_invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained('invoices')->cascadeOnDelete();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('dian_status', 25)->default('pending');
            $table->string('cufe')->nullable();
            $table->string('xml_path')->nullable();
            $table->json('response_json')->nullable();
            $table->text('last_error')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('accepted_at')->nullable();
            $table->timestamps();

            $table->unique(['tenant_id', 'invoice_id']);
            $table->index(['tenant_id', 'dian_status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('electronic_invoices');
    }
};
