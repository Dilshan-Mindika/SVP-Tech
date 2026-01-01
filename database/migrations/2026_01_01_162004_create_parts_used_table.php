<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('parts_used', function (Blueprint $table) {
            $table->id();
            $table->foreignId('repair_job_id')->constrained('repair_jobs')->onDelete('cascade');
            $table->foreignId('supplier_id')->nullable()->constrained('suppliers')->onDelete('set null');
            $table->string('part_name');
            $table->decimal('part_cost', 10, 2);
            $table->integer('quantity_used')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('parts_used');
    }
};
