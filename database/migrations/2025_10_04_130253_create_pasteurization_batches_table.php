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
        Schema::create('pasteurization_batches', function (Blueprint $table) {
            $table->id('batch_id');
            
            // Unique batch number for identification
            $table->string('batch_number')->unique()->comment('Unique identifier for batch (e.g., BATCH-001)');
            
            // Volume tracking
            $table->decimal('total_volume', 10, 2)->comment('Total volume when batch was created');
            $table->decimal('available_volume', 10, 2)->comment('Remaining volume available for dispensing');
            
            // Pasteurization details
            $table->date('date_pasteurized');
            $table->time('time_pasteurized');
            
            // Admin who performed pasteurization (no foreign key constraint)
            $table->unsignedBigInteger('admin_id')->comment('Reference to admin table');
            
            // Batch status
            $table->enum('status', ['active', 'depleted'])->default('active')->comment('Active: has available volume, Depleted: fully dispensed');
            
            // Additional tracking
            $table->text('notes')->nullable()->comment('Any notes about the pasteurization process');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pasteurization_batches');
    }
};
