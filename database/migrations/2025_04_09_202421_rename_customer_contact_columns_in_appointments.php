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
            // Kiểm tra xem cột customer_email và customer_phone có tồn tại không
            if (Schema::hasColumn('appointments', 'customer_email')) {
                // Xóa cột email và phone đã tạo
                $table->dropColumn(['email', 'phone']);
                
                // Đổi tên cột từ customer_email và customer_phone thành email và phone
                $table->renameColumn('customer_email', 'email');
                $table->renameColumn('customer_phone', 'phone');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            // Đổi tên trở lại
            if (Schema::hasColumn('appointments', 'email')) {
                $table->renameColumn('email', 'customer_email');
                $table->renameColumn('phone', 'customer_phone');
            }
        });
    }
};
