<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('disposed_milk', function (Blueprint $table) {
            $table->bigIncrements('disposed_id');
            $table->unsignedBigInteger('source_donation_id')->nullable();
            $table->unsignedBigInteger('source_batch_id')->nullable();
            $table->decimal('volume_disposed', 10, 2)->default(0);
            $table->date('date_disposed')->nullable();
            $table->time('time_disposed')->nullable();
            $table->unsignedBigInteger('admin_id')->nullable();
            $table->text('notes')->nullable();
            $table->json('bag_indices')->nullable();
            $table->timestamps();

            // FKs (nullable for flexibility)
            $table->foreign('source_donation_id')->references('breastmilk_donation_id')->on('breastmilk_donation')->onDelete('set null');
            $table->foreign('source_batch_id')->references('batch_id')->on('pasteurization_batches')->onDelete('set null');
            $table->foreign('admin_id')->references('admin_id')->on('admin')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('disposed_milk');
    }
};
