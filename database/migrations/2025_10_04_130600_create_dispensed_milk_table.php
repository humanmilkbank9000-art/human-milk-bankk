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
        Schema::create('dispensed_milk', function (Blueprint $table) {
            $table->id('dispensed_id');
            
            // Reference to the breastmilk request that was fulfilled (no foreign key constraint)
            $table->unsignedBigInteger('breastmilk_request_id')->comment('Reference to breastmilk_request table');
            
            // Guardian information (user who made the request) - no foreign key constraint
            $table->unsignedBigInteger('guardian_user_id')->comment('Reference to user table - guardian who made request');
            
            // Recipient information (infant who receives the milk) - no foreign key constraint
            $table->unsignedBigInteger('recipient_infant_id')->comment('Reference to infant table - recipient of milk');
            
            // Volume dispensed
            $table->decimal('volume_dispensed', 10, 2)->comment('Actual volume dispensed in ml');
            
            // Dispensing details
            $table->date('date_dispensed');
            $table->time('time_dispensed');
            
            // Admin who performed the dispensing - no foreign key constraint
            $table->unsignedBigInteger('admin_id')->comment('Reference to admin table');
            
            // Additional notes
            $table->text('dispensing_notes')->nullable()->comment('Any notes about the dispensing process');
            
            $table->timestamps();
        });

        // Pivot table for dispensed milk sources (many-to-many relationship)
        Schema::create('dispensed_milk_sources', function (Blueprint $table) {
            $table->id();
            
            // Reference to dispensed milk record
            $table->unsignedBigInteger('dispensed_id');
            $table->foreign('dispensed_id')->references('dispensed_id')->on('dispensed_milk')->onDelete('cascade');
            
            // Source type and reference
            $table->enum('source_type', ['unpasteurized', 'pasteurized']);
            $table->unsignedBigInteger('source_id')->comment('ID of the source (donation or batch)');
            
            // Volume used from this source
            $table->decimal('volume_used', 10, 2)->comment('Volume taken from this source');
            
            $table->timestamps();
            
            // Prevent duplicate sources for the same dispensing
            $table->unique(['dispensed_id', 'source_type', 'source_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dispensed_milk_sources');
        Schema::dropIfExists('dispensed_milk');
    }
};
