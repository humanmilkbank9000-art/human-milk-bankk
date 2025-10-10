<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('breastmilk_donation', function (Blueprint $table) {
            $table->id('breastmilk_donation_id');

            // Foreign key to health screening (one health screening can have multiple donations)
            $table->unsignedBigInteger('health_screening_id');
            $table->foreign('health_screening_id')
                ->references('health_screening_id')
                ->on('health_screening')
                ->onDelete('cascade');

            // Foreign key to admin (who validated the screening)
            $table->unsignedBigInteger('admin_id');
            $table->foreign('admin_id')
                ->references('admin_id')
                ->on('admin')
                ->onDelete('cascade');

            // Foreign key to user (donor)
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')
                ->references('user_id')
                ->on('user')
                ->onDelete('cascade');

            $table->enum('donation_method', ['walk_in', 'home_collection']);
            
            // Status tracking for donation workflow
            $table->enum('status', [
                'pending_walk_in',
                'success_walk_in', 
                'pending_home_collection',
                'scheduled_home_collection',
                'success_home_collection'
            ])->default('pending_walk_in');
            
            // Volume fields - can be filled by user (home collection) or admin (walk-in)
            $table->integer('number_of_bags')->nullable();
            $table->json('individual_bag_volumes')->nullable(); // Array of individual bag volumes
            $table->decimal('total_volume', 10, 2)->nullable();
            
            // Date/time fields
            $table->date('donation_date')->nullable(); // Selected by user for walk-in, assigned by admin for home collection
            $table->time('donation_time')->nullable();
            
            // Home collection scheduling (assigned by admin)
            $table->date('scheduled_pickup_date')->nullable();
            $table->time('scheduled_pickup_time')->nullable();
            
            // Link to admin availability slot for walk-in appointments
            $table->unsignedBigInteger('availability_id')->nullable();

            // Inventory tracking fields
            $table->enum('pasteurization_status', ['unpasteurized', 'pasteurized'])->default('unpasteurized')
                ->comment('Tracks whether this donation has been pasteurized');
            
            $table->unsignedBigInteger('pasteurization_batch_id')->nullable()
                ->comment('Reference to pasteurization_batches table - no foreign key constraint');
            
            $table->decimal('available_volume', 10, 2)->nullable()
                ->comment('Remaining volume available for dispensing (decreases when milk is dispensed)');
            
            $table->enum('inventory_status', ['available', 'depleted'])->default('available')
                ->comment('Available: has volume for dispensing, Depleted: fully dispensed');
            
            $table->timestamp('added_to_inventory_at')->nullable()
                ->comment('When this donation became available in inventory');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('breastmilk_donation');
    }
};
