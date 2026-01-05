<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('repair_jobs', function (Blueprint $table) {
            if (!Schema::hasColumn('repair_jobs', 'job_type')) {
                $table->enum('job_type', ['repair', 'sale'])->default('repair')->after('job_number');
            }
            if (!Schema::hasColumn('repair_jobs', 'invoice_generated')) {
                $table->boolean('invoice_generated')->default(false)->after('repair_status');
            }
        });

        // Use raw SQL to make columns nullable (doctrine/dbal not installed)
        // We can execute these safely as MODIFY works even if it's already nullable
        DB::statement('ALTER TABLE repair_jobs MODIFY laptop_brand VARCHAR(255) NULL');
        DB::statement('ALTER TABLE repair_jobs MODIFY laptop_model VARCHAR(255) NULL');
        DB::statement('ALTER TABLE repair_jobs MODIFY fault_description TEXT NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('repair_jobs', function (Blueprint $table) {
            $table->dropColumn(['job_type', 'invoice_generated']);
            $table->string('laptop_brand')->nullable(false)->change();
            $table->string('laptop_model')->nullable(false)->change();
            $table->text('fault_description')->nullable(false)->change();
        });
    }
};
