<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('breastmilk_donation', function (Blueprint $table) {
            if (!Schema::hasColumn('breastmilk_donation', 'decline_reason')) {
                $table->text('decline_reason')->nullable()->after('status');
            }
            if (!Schema::hasColumn('breastmilk_donation', 'declined_at')) {
                $table->timestamp('declined_at')->nullable()->after('decline_reason');
            }
        });
    }

    public function down(): void
    {
        Schema::table('breastmilk_donation', function (Blueprint $table) {
            if (Schema::hasColumn('breastmilk_donation', 'decline_reason')) {
                $table->dropColumn('decline_reason');
            }
            if (Schema::hasColumn('breastmilk_donation', 'declined_at')) {
                $table->dropColumn('declined_at');
            }
        });
    }
};
