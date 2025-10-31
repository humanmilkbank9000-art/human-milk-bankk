<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('breastmilk_donation')) return;

        Schema::table('breastmilk_donation', function (Blueprint $table) {
            // Store individual bag volumes as JSON array (ml values)
            if (!Schema::hasColumn('breastmilk_donation', 'individual_bag_volumes')) {
                $table->json('individual_bag_volumes')->nullable()->after('number_of_bags')
                    ->comment('JSON array of individual bag volumes in ml (e.g. [500,500])');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('breastmilk_donation')) return;

        Schema::table('breastmilk_donation', function (Blueprint $table) {
            if (Schema::hasColumn('breastmilk_donation', 'individual_bag_volumes')) {
                $table->dropColumn('individual_bag_volumes');
            }
        });
    }
};
