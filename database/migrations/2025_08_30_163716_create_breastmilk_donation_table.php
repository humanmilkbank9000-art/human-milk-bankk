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
            
            // Home collection expression dates
            $table->date('first_expression_date')->nullable();
            $table->date('last_expression_date')->nullable();
            
            // Home collection location
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            
            // Bag details stored as JSON (flexible for any number of bags)
            // Each array element corresponds to one bag with the following structure:
            // [{time: "14:30", date: "2025-10-30", volume: 150, storage_location: "REF", temperature: 4, collection_method: "Manual hands expression"}, ...]
            $table->json('bag_details')->nullable()->comment('Array of bag objects containing time, date, volume, storage_location, temperature, collection_method');
            
            // Lifestyle/health screening questions for home collection
            $table->enum('good_health', ['YES', 'NO'])->nullable()
                ->comment('I am in good health (Maayo akong kahimtang sa akong kalawasan)');
            $table->enum('no_smoking', ['YES', 'NO'])->nullable()
                ->comment('I do not smoke (Dili ako gapangarilyo)');
            $table->enum('no_medication', ['YES', 'NO'])->nullable()
                ->comment('I am not taking medication or herbal supplements (Dili ako gatumar ug mga tambal o supplements)');
            $table->enum('no_alcohol', ['YES', 'NO'])->nullable()
                ->comment('I am not consuming alcohol (Dili ako gainom ug alkohol)');
            $table->enum('no_fever', ['YES', 'NO'])->nullable()
                ->comment('I have not had a fever (Wala ako naghilanat)');
            $table->enum('no_cough_colds', ['YES', 'NO'])->nullable()
                ->comment('I have not had cough or colds (Wala ako mag-ubo o sip-on)');
            $table->enum('no_breast_infection', ['YES', 'NO'])->nullable()
                ->comment('I have no breast infections (Wala ako impeksyon sa akong totoy)');
            $table->enum('followed_hygiene', ['YES', 'NO'])->nullable()
                ->comment('I have followed all hygiene instructions (Gisunod nako ang tanan mga instruksyon tumong sa kalimpyohanon)');
            $table->enum('followed_labeling', ['YES', 'NO'])->nullable()
                ->comment('I have followed all labeling instructions (Gisunod nako ang tanan mga instruksyon tumong sa pagmarka)');
            $table->enum('followed_storage', ['YES', 'NO'])->nullable()
                ->comment('I have followed all storage instructions (Gisunod nako ang tanan mga instruksyon tumong sa pag-tipig sa gatas)');
            
            // Volume tracking fields - CRITICAL: Do not confuse these fields
            $table->decimal('total_volume', 10, 2)->nullable()
                ->comment('IMMUTABLE: Original donation volume - NEVER changes after initial recording');
            
            $table->decimal('dispensed_volume', 10, 2)->default(0)
                ->comment('CUMULATIVE: Total volume dispensed from this donation (sum of all dispensing transactions)');
            
            $table->decimal('available_volume', 10, 2)->nullable()
                ->comment('CALCULATED: Remaining volume available for dispensing (total_volume - dispensed_volume)');
            
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
            
            $table->timestamp('added_to_inventory_at')->nullable()
                ->comment('When this donation became available in inventory');
            
            $table->date('expiration_date')->nullable()
                ->comment('Expiration date: 6 months from donation date for unpasteurized, 1 year for pasteurized');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('breastmilk_donation');
    }
};
