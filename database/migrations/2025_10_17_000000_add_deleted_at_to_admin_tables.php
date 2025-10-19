<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('health_screening', function (Blueprint $table) {
            if (!Schema::hasColumn('health_screening', 'deleted_at')) {
                $table->softDeletes();
            }
        });

        Schema::table('breastmilk_request', function (Blueprint $table) {
            if (!Schema::hasColumn('breastmilk_request', 'deleted_at')) {
                $table->softDeletes();
            }
        });

        Schema::table('breastmilk_donation', function (Blueprint $table) {
            if (!Schema::hasColumn('breastmilk_donation', 'deleted_at')) {
                $table->softDeletes();
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('health_screening', function (Blueprint $table) {
            if (Schema::hasColumn('health_screening', 'deleted_at')) {
                $table->dropSoftDeletes();
            }
        });

        Schema::table('breastmilk_request', function (Blueprint $table) {
            if (Schema::hasColumn('breastmilk_request', 'deleted_at')) {
                $table->dropSoftDeletes();
            }
        });

        Schema::table('breastmilk_donation', function (Blueprint $table) {
            if (Schema::hasColumn('breastmilk_donation', 'deleted_at')) {
                $table->dropSoftDeletes();
            }
        });
    }
};
