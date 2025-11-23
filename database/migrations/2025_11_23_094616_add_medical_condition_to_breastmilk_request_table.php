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
        Schema::table('breastmilk_request', function (Blueprint $table) {
            $table->text('medical_condition')->nullable()->after('prescription_mime_type')->comment('Medical condition or reason for requesting breastmilk');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('breastmilk_request', function (Blueprint $table) {
            $table->dropColumn('medical_condition');
        });
    }
};
