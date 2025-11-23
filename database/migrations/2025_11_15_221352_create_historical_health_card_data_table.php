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
        Schema::create('historical_health_card_data', function (Blueprint $table) {
            $table->id();
            $table->date('record_date');
            $table->integer('issued_count')->default(0);
            $table->integer('claimed_count')->default(0);
            $table->text('notes')->nullable();
            $table->string('data_source')->default('manual'); // manual or system
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            // Index for faster queries
            $table->index('record_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('historical_health_card_data');
    }
};
