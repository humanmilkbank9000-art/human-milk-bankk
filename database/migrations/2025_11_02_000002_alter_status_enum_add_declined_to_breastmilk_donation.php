<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Add 'declined' to the status enum
        if (DB::connection()->getDriverName() === 'sqlite') {
            // SQLite doesn't support MODIFY/ENUM in this way; skip for tests
            return;
        }
        DB::statement("ALTER TABLE `breastmilk_donation` 
            MODIFY COLUMN `status` ENUM(
                'pending_walk_in',
                'success_walk_in',
                'pending_home_collection',
                'scheduled_home_collection',
                'success_home_collection',
                'declined'
            ) NOT NULL DEFAULT 'pending_walk_in'
        ");
    }

    public function down(): void
    {
        // Remove 'declined' from the status enum (for rollback)
        if (DB::connection()->getDriverName() === 'sqlite') {
            return;
        }
        DB::statement("ALTER TABLE `breastmilk_donation` 
            MODIFY COLUMN `status` ENUM(
                'pending_walk_in',
                'success_walk_in',
                'pending_home_collection',
                'scheduled_home_collection',
                'success_home_collection'
            ) NOT NULL DEFAULT 'pending_walk_in'
        ");
    }
};
