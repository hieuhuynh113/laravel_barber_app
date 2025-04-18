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
        Schema::table('invoices', function (Blueprint $table) {
            // Thêm các trường mới
            $table->foreignId('user_id')->nullable()->after('appointment_id')->constrained()->nullOnDelete();
            $table->foreignId('barber_id')->nullable()->after('user_id')->constrained()->nullOnDelete();
            $table->string('invoice_code', 20)->unique()->after('id');
            $table->decimal('subtotal', 10, 2)->after('invoice_number');
            $table->decimal('discount', 10, 2)->default(0)->after('subtotal');
            $table->decimal('tax', 10, 2)->default(0)->after('discount');
            $table->decimal('total', 10, 2)->after('tax');
            $table->enum('status', ['pending', 'completed', 'canceled'])->default('completed')->after('payment_status');
            
            // Đổi tên trường amount thành total_amount nếu cần
            if (Schema::hasColumn('invoices', 'amount')) {
                $table->renameColumn('amount', 'total_amount');
            }
            
            // Mở rộng các enum
            $table->dropColumn('payment_method');
            $table->enum('payment_method', ['cash', 'bank_transfer', 'card', 'momo', 'zalopay'])->default('cash')->after('total');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            // Xóa các trường mới
            $table->dropColumn([
                'invoice_code',
                'user_id',
                'barber_id',
                'subtotal',
                'discount',
                'tax',
                'status'
            ]);
            
            // Đổi tên trường total_amount thành amount nếu đã đổi
            if (Schema::hasColumn('invoices', 'total_amount')) {
                $table->renameColumn('total_amount', 'amount');
            }
            
            // Khôi phục enum payment_method
            $table->dropColumn('payment_method');
            $table->enum('payment_method', ['cash', 'bank_transfer'])->default('cash');
        });
    }
};
