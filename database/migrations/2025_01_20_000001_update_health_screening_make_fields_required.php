<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Sanitize existing values before applying strict enum changes to avoid data truncation
        // Replace NULLs or any non-conforming values with a safe default ('no')
        $cols = [
            'medical_history_02','medical_history_03','medical_history_04','medical_history_05','medical_history_06','medical_history_07','medical_history_08','medical_history_09','medical_history_10','medical_history_11','medical_history_12','medical_history_13','medical_history_14','medical_history_15',
            'sexual_history_01','sexual_history_02','sexual_history_03','sexual_history_04',
            'donor_infant_01','donor_infant_02','donor_infant_03','donor_infant_04','donor_infant_05'
        ];

        foreach ($cols as $col) {
            // Only touch the table if the column exists to avoid errors on some environments
            if (Schema::hasColumn('health_screening', $col)) {
                // Set NULL values to 'no'
                DB::table('health_screening')->whereNull($col)->update([$col => 'no']);
                // Set any values not 'yes' or 'no' to 'no' (covers empty string or legacy values)
                DB::table('health_screening')->whereNotIn($col, ['yes', 'no'])->update([$col => 'no']);
            }
        }

        Schema::table('health_screening', function (Blueprint $table) {
            // Make all medical history fields NOT nullable since they're all required
            $table->enum('medical_history_02', ['yes','no'])->nullable(false)->change();
            $table->enum('medical_history_03', ['yes','no'])->nullable(false)->change();
            $table->enum('medical_history_04', ['yes','no'])->nullable(false)->change();
            $table->enum('medical_history_05', ['yes','no'])->nullable(false)->change();
            $table->enum('medical_history_06', ['yes','no'])->nullable(false)->change();
            $table->enum('medical_history_07', ['yes','no'])->nullable(false)->change();
            $table->enum('medical_history_08', ['yes','no'])->nullable(false)->change();
            $table->enum('medical_history_09', ['yes','no'])->nullable(false)->change();
            $table->enum('medical_history_10', ['yes','no'])->nullable(false)->change();
            $table->enum('medical_history_11', ['yes','no'])->nullable(false)->change();
            $table->enum('medical_history_12', ['yes','no'])->nullable(false)->change();
            $table->enum('medical_history_13', ['yes','no'])->nullable(false)->change();
            $table->enum('medical_history_14', ['yes','no'])->nullable(false)->change();
            $table->enum('medical_history_15', ['yes','no'])->nullable(false)->change();

            // Make all sexual history fields NOT nullable
            $table->enum('sexual_history_01', ['yes','no'])->nullable(false)->change();
            $table->enum('sexual_history_02', ['yes','no'])->nullable(false)->change();
            $table->enum('sexual_history_03', ['yes','no'])->nullable(false)->change();
            $table->enum('sexual_history_04', ['yes','no'])->nullable(false)->change();

            // Make all donor infant fields NOT nullable
            $table->enum('donor_infant_01', ['yes','no'])->nullable(false)->change();
            $table->enum('donor_infant_02', ['yes','no'])->nullable(false)->change();
            $table->enum('donor_infant_03', ['yes','no'])->nullable(false)->change();
            $table->enum('donor_infant_04', ['yes','no'])->nullable(false)->change();
            $table->enum('donor_infant_05', ['yes','no'])->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('health_screening', function (Blueprint $table) {
            // Revert back to nullable
            $table->enum('medical_history_02', ['yes','no'])->nullable()->change();
            $table->enum('medical_history_03', ['yes','no'])->nullable()->change();
            $table->enum('medical_history_04', ['yes','no'])->nullable()->change();
            $table->enum('medical_history_05', ['yes','no'])->nullable()->change();
            $table->enum('medical_history_06', ['yes','no'])->nullable()->change();
            $table->enum('medical_history_07', ['yes','no'])->nullable()->change();
            $table->enum('medical_history_08', ['yes','no'])->nullable()->change();
            $table->enum('medical_history_09', ['yes','no'])->nullable()->change();
            $table->enum('medical_history_10', ['yes','no'])->nullable()->change();
            $table->enum('medical_history_11', ['yes','no'])->nullable()->change();
            $table->enum('medical_history_12', ['yes','no'])->nullable()->change();
            $table->enum('medical_history_13', ['yes','no'])->nullable()->change();
            $table->enum('medical_history_14', ['yes','no'])->nullable()->change();
            $table->enum('medical_history_15', ['yes','no'])->nullable()->change();

            $table->enum('sexual_history_01', ['yes','no'])->nullable()->change();
            $table->enum('sexual_history_02', ['yes','no'])->nullable()->change();
            $table->enum('sexual_history_03', ['yes','no'])->nullable()->change();
            $table->enum('sexual_history_04', ['yes','no'])->nullable()->change();

            $table->enum('donor_infant_01', ['yes','no'])->nullable()->change();
            $table->enum('donor_infant_02', ['yes','no'])->nullable()->change();
            $table->enum('donor_infant_03', ['yes','no'])->nullable()->change();
            $table->enum('donor_infant_04', ['yes','no'])->nullable()->change();
            $table->enum('donor_infant_05', ['yes','no'])->nullable()->change();
        });
    }
};