<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('admin', function (Blueprint $table): void {
            $table->id('admin_id');
            $table->string('full_name');
            $table->string('contact_number', 11);
            $table->string('username')->unique();
            $table->string('password');
            $table->timestamps();
        });

        // Insert default admin
        DB::table('admin')->insert([
            'full_name'      => 'System Admin',
            'contact_number' => '09123456789',
            'username'       => 'adminhmblsc',
            'password'       => Hash::make('admin123'),
            'created_at'     => now(),
            'updated_at'     => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin');
    }
};
