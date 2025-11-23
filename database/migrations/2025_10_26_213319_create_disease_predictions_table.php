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
        Schema::create('disease_predictions', function (Blueprint $table) {
            $table->id();
            $table->enum('disease_type', ['hiv', 'dengue', 'malaria', 'measles', 'rabies', 'pregnancy_complications']);
            $table->foreignId('barangay_id')->nullable()->constrained()->onDelete('cascade'); // null = city-wide
            $table->date('prediction_date');
            $table->decimal('predicted_cases', 8, 2);
            $table->decimal('confidence_interval_lower', 8, 2);
            $table->decimal('confidence_interval_upper', 8, 2);
            $table->string('model_version')->default('v1.0');
            $table->json('accuracy_metrics')->nullable();
            $table->timestamps();

            $table->index(['disease_type', 'prediction_date']);
            $table->index(['barangay_id', 'prediction_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('disease_predictions');
    }
};
