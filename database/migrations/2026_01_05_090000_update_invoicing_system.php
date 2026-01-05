<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Update Customers Table
        Schema::table('customers', function (Blueprint $table) {
            $table->enum('type', ['normal', 'shop'])->default('normal')->after('email');
        });

        // 2. Update Invoices Table
        Schema::table('invoices', function (Blueprint $table) {
            $table->decimal('paid_amount', 10, 2)->default(0)->after('total_amount');
            $table->enum('status', ['unpaid', 'partial', 'paid'])->default('unpaid')->after('paid_amount');
        });

        // 3. Create Payments Table
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained('invoices')->onDelete('cascade');
            $table->decimal('amount', 10, 2);
            $table->date('payment_date');
            $table->enum('payment_method', ['cash', 'card', 'bank_transfer', 'cheque']);
            $table->string('reference_number')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');

        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn(['paid_amount', 'status']);
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
};
