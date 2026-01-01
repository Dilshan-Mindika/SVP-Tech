<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('repair_job_id')->constrained('repair_jobs')->onDelete('cascade');
            $table->enum('invoice_type', ['job', 'service']); // Job = estimate/initial, Service = final
            $table->decimal('total_amount', 10, 2);
            $table->decimal('parts_cost', 10, 2)->default(0);
            $table->decimal('labor_cost', 10, 2)->default(0);
            $table->decimal('profit_margin', 10, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
