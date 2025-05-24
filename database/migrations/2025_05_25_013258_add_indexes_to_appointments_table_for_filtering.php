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
        Schema::table('appointments', function (Blueprint $table) {
            // Thêm index cho các trường thường được sử dụng để lọc
            $table->index('status', 'idx_appointments_status');
            $table->index('appointment_date', 'idx_appointments_date');
            $table->index('barber_id', 'idx_appointments_barber_id');

            // Composite index cho các truy vấn phức tạp
            $table->index(['barber_id', 'status'], 'idx_appointments_barber_status');
            $table->index(['barber_id', 'appointment_date'], 'idx_appointments_barber_date');
            $table->index(['status', 'appointment_date'], 'idx_appointments_status_date');

            // Index cho truy vấn theo tháng và năm
            $table->index(['barber_id', 'status', 'appointment_date'], 'idx_appointments_barber_status_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            // Xóa các index đã tạo
            $table->dropIndex('idx_appointments_status');
            $table->dropIndex('idx_appointments_date');
            $table->dropIndex('idx_appointments_barber_id');
            $table->dropIndex('idx_appointments_barber_status');
            $table->dropIndex('idx_appointments_barber_date');
            $table->dropIndex('idx_appointments_status_date');
            $table->dropIndex('idx_appointments_barber_status_date');
        });
    }
};
