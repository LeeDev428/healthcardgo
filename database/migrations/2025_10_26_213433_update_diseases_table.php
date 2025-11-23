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
        Schema::table('diseases', function (Blueprint $table) {
            $table->foreignId('patient_id')->after('id')->constrained()->onDelete('cascade');
            $table->foreignId('medical_record_id')->nullable()->after('patient_id')->constrained()->onDelete('set null');
            $table->date('diagnosis_date')->after('confirmed_date');
            $table->enum('severity', ['mild', 'moderate', 'severe'])->nullable()->after('treatment_notes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('diseases', function (Blueprint $table) {
            $table->dropForeign(['patient_id']);
            $table->dropForeign(['medical_record_id']);
            $table->dropColumn(['patient_id', 'medical_record_id', 'diagnosis_date', 'severity']);
        });
    }
};
