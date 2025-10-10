<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('health_screening', function (Blueprint $table) {
            $table->id('health_screening_id');

            // Required foreign keys
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('user_id')->on('user')->onDelete('cascade');

            $table->unsignedBigInteger('infant_id')->nullable();
            $table->foreign('infant_id')->references('infant_id')->on('infant')->onDelete('set null');

            // Basic info
            $table->enum('civil_status', ['single', 'married', 'divorced', 'widowed']);
            $table->string('occupation');
            $table->enum('type_of_donor', ['community', 'private', 'employee', 'network_office_agency']);
            $table->enum('status', ['pending', 'accepted', 'declined'])->default('pending');
            $table->timestamp('date_accepted')->nullable();
            $table->timestamp('date_declined')->nullable();
            $table->text('admin_notes')->nullable();

            // Medical history
            $table->enum('medical_history_01', ['yes','no']);
            $table->string('medical_history_02')->nullable();
            $table->enum('medical_history_03', ['yes','no']);
            $table->string('medical_history_04')->nullable();
            $table->string('medical_history_05')->nullable();
            $table->string('medical_history_06')->nullable();
            $table->enum('medical_history_07', ['yes','no']);
            $table->string('medical_history_08')->nullable();
            $table->enum('medical_history_09', ['yes','no']);
            $table->string('medical_history_10')->nullable();
            $table->string('medical_history_11')->nullable();
            $table->string('medical_history_12')->nullable();
            $table->string('medical_history_13')->nullable();
            $table->enum('medical_history_14', ['yes','no']);
            $table->enum('medical_history_15', ['yes','no']);

            // Sexual history
            $table->enum('sexual_history_01', ['yes','no']);
            $table->enum('sexual_history_02', ['yes','no']);
            $table->string('sexual_history_03')->nullable();
            $table->enum('sexual_history_04', ['yes','no']);

            // Donor infant history
            $table->enum('donor_infant_01', ['yes','no']);
            $table->enum('donor_infant_02', ['yes','no']);
            $table->enum('donor_infant_03', ['yes','no']);
            $table->string('donor_infant_04')->nullable();
            $table->string('donor_infant_05')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('health_screening');
    }
};
