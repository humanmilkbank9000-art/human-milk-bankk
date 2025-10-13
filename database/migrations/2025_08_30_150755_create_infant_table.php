<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('infant', function (Blueprint $table) {
            $table->id('infant_id');
            $table->unsignedBigInteger('user_id');
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->string('suffix')->nullable();
            $table->enum('sex', ['female', 'male']);
            $table->date('date_of_birth');
            $table->integer('age');
            $table->decimal('birth_weight', 5, 2); // e.g. 4.50 kg
            $table->timestamps();

            $table->foreign('user_id')
                ->references('user_id')
                ->on('user')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('infant');
    }
};
