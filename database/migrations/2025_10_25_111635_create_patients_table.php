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
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->date('date_of_birth');
            $table->enum('gender', ['male', 'female']);
            $table->foreignId('barangay_id')->nullable()->constrained();
            $table->string('patient_number')->unique();
            $table->string('philhealth_number')->nullable();
            $table->json('medical_history')->nullable();
            $table->json('allergies')->nullable();
            $table->json('current_medications')->nullable();
            $table->json('insurance_info')->nullable();
            $table->json('emergency_contact')->nullable();
            $table->text('accessibility_requirements')->nullable();
            $table->timestamps();

            $table->index(['patient_number']);
            $table->index(['philhealth_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};
