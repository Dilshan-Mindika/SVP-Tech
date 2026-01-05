<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('repair_jobs', function (Blueprint $table) {
            $table->enum('payment_status', ['pending', 'partial', 'paid'])->default('pending')->after('repair_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('repair_jobs', function (Blueprint $table) {
            $table->dropColumn('payment_status');
        });
    }
};
