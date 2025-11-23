<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1) Drop existing FK to allow column alteration
        Schema::table('patients', function (Blueprint $table) {
            try {
                $table->dropForeign(['user_id']);
            } catch (\Throwable $e) {
                // FK may not exist in some environments
            }
        });

        // 2) Make column nullable using Laravel 12 native column modification
        Schema::table('patients', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->change();
        });

        // 3) Recreate FK with SET NULL on delete
        Schema::table('patients', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        // Revert to NOT NULL with CASCADE delete
        Schema::table('patients', function (Blueprint $table) {
            try {
                $table->dropForeign(['user_id']);
            } catch (\Throwable $e) {
                // FK may not exist
            }
        });

        Schema::table('patients', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable(false)->change();
        });

        Schema::table('patients', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }
};
