<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('perfumes', function (Blueprint $table) {
            $table->unsignedInteger('stock_10ml')->nullable()->after('stock');
            $table->unsignedInteger('stock_50ml')->nullable()->after('stock_10ml');
        });
    }

    public function down(): void
    {
        Schema::table('perfumes', function (Blueprint $table) {
            $table->dropColumn(['stock_10ml', 'stock_50ml']);
        });
    }
};
