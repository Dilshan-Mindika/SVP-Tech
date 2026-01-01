<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('repair_jobs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->foreignId('technician_id')->nullable()->constrained()->onDelete('set null'); // Job might not be assigned immediately
            $table->string('laptop_brand');
            $table->string('laptop_model');
            $table->string('serial_number')->nullable();
            $table->text('fault_description');
            $table->enum('repair_status', ['pending', 'in_progress', 'completed', 'delivered'])->default('pending');
            $table->text('repair_notes')->nullable();
            
            // Financials
            $table->decimal('parts_used_cost', 10, 2)->default(0);
            $table->decimal('labor_cost', 10, 2)->default(0);
            $table->decimal('final_price', 10, 2)->default(0);

            // Timestamps for invoicing
            $table->timestamp('job_invoice_generated_at')->nullable();
            $table->timestamp('service_invoice_generated_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('repair_jobs');
    }
};
