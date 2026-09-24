<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('wishlists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('perfume_id')->constrained('perfumes')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['user_id', 'perfume_id']);
        });

        Schema::create('perfume_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('perfume_id')->constrained('perfumes')->cascadeOnDelete();
            $table->unsignedTinyInteger('rating');
            $table->text('body');
            $table->string('image_path')->nullable();
            $table->timestamps();
            $table->unique(['user_id', 'perfume_id']);
        });

        Schema::create('stock_alerts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('perfume_id')->constrained('perfumes')->cascadeOnDelete();
            $table->timestamp('notified_at')->nullable();
            $table->timestamps();
            $table->unique(['user_id', 'perfume_id']);
        });

        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->enum('type', ['fixed', 'percent']);
            $table->unsignedInteger('value');
            $table->unsignedInteger('minimum_order')->default(0);
            $table->unsignedInteger('usage_limit')->nullable();
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->string('coupon_code')->nullable();
            $table->unsignedInteger('discount_amount')->default(0);
            $table->unsignedInteger('points_used')->default(0);
        });
    }

    public function down(): void
    {
        Schema::table('orders', fn (Blueprint $table) => $table->dropColumn(['coupon_code', 'discount_amount', 'points_used']));
        Schema::dropIfExists('coupons');
        Schema::dropIfExists('stock_alerts');
        Schema::dropIfExists('perfume_reviews');
        Schema::dropIfExists('wishlists');
    }
};
