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
        DB::statement("ALTER TABLE repair_jobs MODIFY COLUMN repair_status ENUM('pending', 'in_progress', 'waiting_for_parts', 'completed', 'delivered', 'cancelled') DEFAULT 'pending'");
    }

    public function down(): void
    {
        // Revert to original enum (WARNING: data might be truncated if not handled, but for down it's acceptable dev practice)
        DB::statement("ALTER TABLE repair_jobs MODIFY COLUMN repair_status ENUM('pending', 'in_progress', 'completed', 'delivered') DEFAULT 'pending'");
    }
};
