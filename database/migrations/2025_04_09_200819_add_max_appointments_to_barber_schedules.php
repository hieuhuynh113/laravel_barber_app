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
        Schema::table('barber_schedules', function (Blueprint $table) {
            $table->integer('max_appointments')->default(3)->after('is_day_off');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('barber_schedules', function (Blueprint $table) {
            $table->dropColumn('max_appointments');
        });
    }
};
