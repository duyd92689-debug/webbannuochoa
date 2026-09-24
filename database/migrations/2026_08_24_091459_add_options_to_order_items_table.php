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
        Schema::table('order_items', function (Blueprint $table) {
            $table->unsignedInteger('volume_ml')->nullable()->after('quantity');
            $table->boolean('addon_gift')->default(false)->after('volume_ml');
            $table->string('engrave_text', 100)->nullable()->after('addon_gift');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn(['volume_ml', 'addon_gift', 'engrave_text']);
        });
    }
};
