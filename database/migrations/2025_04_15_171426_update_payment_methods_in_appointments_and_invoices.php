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
        // Cập nhật phương thức thanh toán trong bảng appointments
        Schema::table('appointments', function (Blueprint $table) {
            // Xóa enum payment_method hiện tại
            $table->dropColumn('payment_method');

            // Thêm lại enum payment_method mới chỉ với cash và bank_transfer
            $table->enum('payment_method', ['cash', 'bank_transfer'])->default('cash')->after('phone');
        });

        // Cập nhật phương thức thanh toán trong bảng invoices
        Schema::table('invoices', function (Blueprint $table) {
            // Xóa enum payment_method hiện tại
            $table->dropColumn('payment_method');

            // Thêm lại enum payment_method mới chỉ với cash, bank_transfer và card
            $table->enum('payment_method', ['cash', 'bank_transfer', 'card'])->default('cash')->after('total');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Khôi phục phương thức thanh toán trong bảng appointments
        Schema::table('appointments', function (Blueprint $table) {
            // Xóa enum payment_method hiện tại
            $table->dropColumn('payment_method');

            // Thêm lại enum payment_method cũ bao gồm cả momo và zalopay
            $table->enum('payment_method', ['cash', 'bank_transfer', 'card', 'momo', 'zalopay'])->default('cash')->after('phone');
        });

        // Khôi phục phương thức thanh toán trong bảng invoices
        Schema::table('invoices', function (Blueprint $table) {
            // Xóa enum payment_method hiện tại
            $table->dropColumn('payment_method');

            // Thêm lại enum payment_method cũ bao gồm cả momo và zalopay
            $table->enum('payment_method', ['cash', 'bank_transfer', 'card', 'momo', 'zalopay'])->default('cash')->after('total');
        });
    }
};
