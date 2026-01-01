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
            $table->index('repair_status');
            $table->index('created_at');
            $table->index('updated_at');
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->index('phone');
            $table->index('name');
        });

        Schema::table('parts', function (Blueprint $table) {
            $table->index('name');
        });
    }

    public function down(): void
    {
        Schema::table('repair_jobs', function (Blueprint $table) {
            $table->dropIndex(['repair_status', 'created_at', 'updated_at']);
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->dropIndex(['phone', 'name']);
        });

        Schema::table('parts', function (Blueprint $table) {
            $table->dropIndex(['name']);
        });
    }
};
