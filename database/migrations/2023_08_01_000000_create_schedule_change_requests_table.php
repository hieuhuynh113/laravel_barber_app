<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('schedule_change_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('barber_id')->constrained()->onDelete('cascade');
            $table->tinyInteger('day_of_week')->comment('0: Sunday, 1-6: Monday to Saturday');
            $table->time('start_time');
            $table->time('end_time');
            $table->boolean('is_day_off')->default(false);
            $table->text('reason');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('admin_notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedule_change_requests');
    }
};
