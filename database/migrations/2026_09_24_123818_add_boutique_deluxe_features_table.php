<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // 1. Quà tặng cao cấp & lời nhắn cho Orders
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'gift_wrap')) {
                $table->string('gift_wrap')->nullable()->after('points_used');
            }
            if (!Schema::hasColumn('orders', 'gift_card')) {
                $table->string('gift_card')->nullable()->after('gift_wrap');
            }
            if (!Schema::hasColumn('orders', 'gift_message')) {
                $table->text('gift_message')->nullable()->after('gift_card');
            }
            if (!Schema::hasColumn('orders', 'gift_delivery_date')) {
                $table->date('gift_delivery_date')->nullable()->after('gift_message');
            }
        });

        // 2. Tủ nước hoa cá nhân (Scent Wardrobes)
        if (!Schema::hasTable('scent_wardrobes')) {
            Schema::create('scent_wardrobes', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->foreignId('perfume_id')->constrained('perfumes')->cascadeOnDelete();
                $table->string('occasion')->default('work'); // work, date, party, casual
                $table->text('notes')->nullable();
                $table->timestamps();
                $table->unique(['user_id', 'perfume_id', 'occasion']);
            });
        }

        // 3. Khởi tạo sẵn các mã khuyến mãi cho Vòng quay may mắn & Mùi hương hôm nay
        $now = now();
        $coupons = [
            [
                'code' => 'SPIN50K',
                'type' => 'fixed',
                'value' => 50000,
                'minimum_order' => 300000,
                'usage_limit' => 1000,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'code' => 'FREESHIP',
                'type' => 'fixed',
                'value' => 30000,
                'minimum_order' => 200000,
                'usage_limit' => 1000,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'code' => 'SPIN10',
                'type' => 'percent',
                'value' => 10,
                'minimum_order' => 500000,
                'usage_limit' => 1000,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'code' => 'SPIN100K',
                'type' => 'fixed',
                'value' => 100000,
                'minimum_order' => 1000000,
                'usage_limit' => 500,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'code' => 'TODAY10',
                'type' => 'percent',
                'value' => 10,
                'minimum_order' => 0,
                'usage_limit' => 2000,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        foreach ($coupons as $coupon) {
            DB::table('coupons')->updateOrInsert(['code' => $coupon['code']], $coupon);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('scent_wardrobes');
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['gift_wrap', 'gift_card', 'gift_message', 'gift_delivery_date']);
        });
    }
};
