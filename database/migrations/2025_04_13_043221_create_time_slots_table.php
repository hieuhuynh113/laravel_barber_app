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
        Schema::create('time_slots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('barber_id')->constrained('barbers');
            $table->date('date');
            $table->string('time_slot');
            $table->integer('booked_count')->default(0);
            $table->integer('max_bookings')->default(2);
            $table->timestamps();

            // Tạo unique constraint để đảm bảo không có trùng lặp
            $table->unique(['barber_id', 'date', 'time_slot']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('time_slots');
    }
};
