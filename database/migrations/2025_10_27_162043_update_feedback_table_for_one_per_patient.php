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
        Schema::table('feedback', function (Blueprint $table) {
            // Make appointment_id nullable since we're allowing one feedback per patient
            $table->foreignId('appointment_id')->nullable()->change();

            // Add unique constraint on patient_id to ensure one feedback per patient
            $table->unique('patient_id', 'feedback_patient_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('feedback', function (Blueprint $table) {
            $table->dropUnique('feedback_patient_unique');
            $table->foreignId('appointment_id')->nullable(false)->change();
        });
    }
};
