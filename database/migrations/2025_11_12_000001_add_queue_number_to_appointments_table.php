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
        Schema::table('appointments', function (Blueprint $table) {
            // Add queue_number if it doesn't exist yet
            if (! Schema::hasColumn('appointments', 'queue_number')) {
                $table->unsignedInteger('queue_number')->after('scheduled_at');
                $table->index('queue_number');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            if (Schema::hasColumn('appointments', 'queue_number')) {
                $table->dropIndex(['queue_number']);
                $table->dropColumn('queue_number');
            }
        });
    }
};
