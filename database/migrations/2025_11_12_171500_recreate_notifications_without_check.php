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
        // SQLite cannot drop CHECK constraints directly. Recreate table without CHECK constraint on `type`.
        if (DB::getDriverName() !== 'sqlite') {
            // No-op for non-SQLite drivers; base migration already defines schema without CHECK.
            return;
        }

        Schema::create('notifications_tmp', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('type', 100);
            $table->string('title');
            $table->text('message');
            $table->json('data')->nullable();
            $table->timestamp('read_at')->nullable()->index();
            $table->timestamps();

            $table->index(['user_id', 'read_at']);
        });

        // Copy data from old table
        DB::statement('INSERT INTO notifications_tmp (id, created_at, updated_at, user_id, type, title, message, data, read_at)
                       SELECT id, created_at, updated_at, user_id, type, title, message, data, read_at FROM notifications');

        Schema::drop('notifications');
        Schema::rename('notifications_tmp', 'notifications');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Irreversible (would require re-adding the previous CHECK constraint, which is unknown).
    }
};
