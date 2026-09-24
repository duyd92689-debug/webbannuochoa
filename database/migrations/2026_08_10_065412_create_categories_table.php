<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // The earlier migration already owns the categories table.
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Intentionally blank so rolling back does not remove that table.
    }
};
