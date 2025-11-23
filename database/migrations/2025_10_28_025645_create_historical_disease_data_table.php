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
        Schema::create('historical_disease_data', function (Blueprint $table) {
            $table->id();
            $table->enum('disease_type', ['hiv', 'dengue', 'malaria', 'measles', 'rabies', 'pregnancy_complications']);
            $table->foreignId('barangay_id')->nullable()->constrained()->onDelete('cascade');
            $table->date('record_date'); // Year-Month for monthly data
            $table->integer('case_count')->default(0);
            $table->text('notes')->nullable();
            $table->enum('data_source', ['manual', 'imported', 'system'])->default('manual');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();

            $table->index(['disease_type', 'record_date']);
            $table->index(['barangay_id', 'record_date']);
            $table->unique(['disease_type', 'barangay_id', 'record_date'], 'hist_disease_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('historical_disease_data');
    }
};
