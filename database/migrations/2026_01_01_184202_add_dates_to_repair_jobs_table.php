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
            $table->timestamp('completed_at')->nullable()->after('repair_status');
            $table->timestamp('delivered_at')->nullable()->after('completed_at');
        });
    }

    public function down(): void
    {
        Schema::table('repair_jobs', function (Blueprint $table) {
            $table->dropColumn(['completed_at', 'delivered_at']);
        });
    }
};
