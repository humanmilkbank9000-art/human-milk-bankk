<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admin_availability', function (Blueprint $table): void {
            $table->id(); // Standard primary key for easier referencing in appointments
            
            // Core availability fields
            $table->date('available_date')->comment('The specific date this availability is for');
            $table->time('start_time')->comment('Start time of availability slot');
            $table->time('end_time')->comment('End time of availability slot');
            
            // Status management for appointment system
            $table->enum('status', ['available', 'booked', 'blocked'])->default('available')
                  ->comment('available=open for booking, booked=appointment scheduled, blocked=admin unavailable');
            
            // Optional fields for appointment system
            $table->string('notes')->nullable()->comment('Admin notes about this time slot');
            
            // Future: Reference to admin if multiple admins
            // $table->unsignedBigInteger('admin_id')->default(1);
            
            $table->timestamps();
            
            // Optimized indexes for appointment queries
            $table->index(['available_date', 'status'], 'date_status_idx'); // Find available slots by date
            $table->index(['available_date', 'start_time'], 'date_time_idx'); // Order slots by time
            $table->index('status', 'status_idx'); // Quick status filtering
            
            // Prevent duplicate time slots on same date
            $table->unique(['available_date', 'start_time'], 'unique_date_time_slot');
        });
        
        // Add foreign key constraint to donations table now that availability table exists
        Schema::table('breastmilk_donation', function (Blueprint $table): void {
            $table->foreign('availability_id')
                ->references('id')
                ->on('admin_availability')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        // Drop foreign key first before dropping the referenced table
        Schema::table('breastmilk_donation', function (Blueprint $table): void {
            $table->dropForeign(['availability_id']);
        });
        
        Schema::dropIfExists('admin_availability');
    }
};