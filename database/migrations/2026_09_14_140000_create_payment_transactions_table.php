<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->string('gateway'); // phân biệt các cổng thanh toán khác nhau (ví dụ: PayPal, Stripe, VNPay, Momo, v.v.)
            $table->string('gateway_order_id')->nullable()->index(); // lưu trữ ID đơn hàng từ cổng thanh toán
            $table->string('transaction_id')->nullable()->index(); // lưu trữ ID giao dịch từ cổng thanh toán
            $table->decimal('amount', 15, 2); // lưu trữ số tiền thanh toán
            $table->string('status')->default('pending'); // trạng thái giao dịch thanh toán (pending, completed, failed, canceled, v.v.)
            $table->integer('result_code')->nullable(); // mã kết quả từ cổng thanh toán
            $table->string('message')->nullable(); // thông điệp từ cổng thanh toán
            $table->json('request_payload')->nullable(); // dữ liệu yêu cầu gửi đến cổng thanh toán
            $table->json('response_payload')->nullable(); // dữ liệu phản hồi từ cổng thanh toán
            $table->timestamp('paid_at')->nullable(); // thời gian thanh toán thành công
            $table->timestamps();

            $table->unique(['gateway', 'gateway_order_id']); // đảm bảo mỗi cổng thanh toán chỉ có 1 giao dịch duy nhất cho mỗi gateway_order_id
            $table->index(['order_id', 'status']); // tối ưu hóa truy vấn theo order_id và status
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_transactions');
    }
};
