<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Initial create migration now contains all columns; this migration becomes a no-op to avoid duplicate column errors.
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No-op rollback since no changes applied in up().
    }
};
