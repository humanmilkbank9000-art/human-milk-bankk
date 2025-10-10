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
        Schema::create('breastmilk_request', function (Blueprint $table) {
            $table->id('breastmilk_request_id');

            // Foreign key to user (requester)
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')
                ->references('user_id')
                ->on('user')
                ->onDelete('cascade');

            // Foreign key to infant (for whom the request is made)
            $table->unsignedBigInteger('infant_id');
            $table->foreign('infant_id')
                ->references('infant_id')
                ->on('infant')
                ->onDelete('cascade');

            // Foreign key to admin availability (appointment slot)
            $table->unsignedBigInteger('availability_id')->nullable();
            $table->foreign('availability_id')
                ->references('id')
                ->on('admin_availability')
                ->onDelete('set null');

            // Foreign key to admin (who validates/approves)
            $table->unsignedBigInteger('admin_id')->nullable();
            $table->foreign('admin_id')
                ->references('admin_id')
                ->on('admin')
                ->onDelete('set null');

            // Prescription file storage (File path based storage)
            $table->string('prescription_path')->nullable()->comment('Storage path to prescription file');
            $table->string('prescription_filename')->nullable()->comment('Original filename of prescription');
            $table->string('prescription_mime_type')->nullable()->comment('MIME type of prescription file');

            // Volume requested (admin will set this when approving)
            $table->decimal('volume_requested', 10, 2)->nullable();

            $table->date('request_date');
            $table->time('request_time');

            $table->enum('status', ['pending', 'approved', 'declined', 'dispensed'])->default('pending');
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('declined_at')->nullable();
            $table->text('admin_notes')->nullable();

            // Dispensing tracking fields
            $table->decimal('volume_dispensed', 10, 2)->nullable()->comment('Actual volume dispensed to the recipient');
            $table->timestamp('dispensed_at')->nullable()->comment('When the milk was dispensed');
            $table->text('dispensing_notes')->nullable()->comment('Notes about the dispensing process');
            
            // Reference to the dispensed milk record - no foreign key constraint
            $table->unsignedBigInteger('dispensed_milk_id')->nullable()
                ->comment('Reference to dispensed_milk table - no foreign key constraint');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('breastmilk_request');
    }
};
