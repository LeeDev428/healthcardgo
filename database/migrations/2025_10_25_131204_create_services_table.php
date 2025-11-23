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
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description');
            $table->integer('duration_minutes')->nullable(); // Duration in minutes
            $table->decimal('fee', 8, 2)->nullable();
            $table->string('category')->nullable(); // health_card, hiv_testing, pregnancy_care, vaccination, etc.
            $table->json('requirements')->nullable(); // What patient needs to bring/prepare
            $table->json('preparation_instructions')->nullable();
            $table->boolean('requires_appointment')->default(true);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['category']);
            $table->index(['is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
