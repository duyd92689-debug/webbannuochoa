<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('order_items') && !Schema::hasColumn('order_items', 'product_id')) {
            Schema::table('order_items', function (Blueprint $table) {
                $table->unsignedBigInteger('product_id')->nullable()->after('perfume_id');
            });
            DB::statement('UPDATE order_items SET product_id = perfume_id WHERE product_id IS NULL');
        }

        if (!Schema::hasTable('products')) {
            $driver = DB::getDriverName();
            if ($driver === 'sqlite') {
                DB::statement('CREATE VIEW IF NOT EXISTS products AS SELECT * FROM perfumes');
            } else {
                DB::statement('CREATE OR REPLACE VIEW products AS SELECT * FROM perfumes');
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('order_items', 'product_id')) {
            Schema::table('order_items', function (Blueprint $table) {
                $table->dropColumn('product_id');
            });
        }

        DB::statement('DROP VIEW IF EXISTS products');
    }
};
