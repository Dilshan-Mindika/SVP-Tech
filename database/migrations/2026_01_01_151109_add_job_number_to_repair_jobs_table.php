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
            $table->string('job_number')->nullable()->after('id');
        });

        // Auto-generate for existing records
        $jobs = DB::table('repair_jobs')->get();
        foreach ($jobs as $job) {
            $formattedId = str_pad($job->id, 6, '0', STR_PAD_LEFT);
            DB::table('repair_jobs')
                ->where('id', $job->id)
                ->update(['job_number' => "PWCRJ{$formattedId}"]);
        }

        // Now make it unique and required (optional but good practice)
        Schema::table('repair_jobs', function (Blueprint $table) {
            $table->string('job_number')->unique()->change();
        });
    }

    public function down(): void
    {
        Schema::table('repair_jobs', function (Blueprint $table) {
            $table->dropColumn('job_number');
        });
    }
};
