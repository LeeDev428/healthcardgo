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
        Schema::table('medical_records', function (Blueprint $table) {
            $table->foreignId('appointment_id')->nullable()->after('patient_id')->constrained()->onDelete('set null');
            $table->enum('category', ['general', 'healthcard', 'hiv', 'pregnancy', 'immunization'])->after('service_id');
            $table->string('template_type')->nullable()->after('category');
            $table->json('record_data')->nullable()->after('template_type'); // Stores all template fields
            $table->boolean('is_encrypted')->default(false)->after('record_data');
            $table->foreignId('updated_by')->nullable()->after('created_by')->constrained('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('medical_records', function (Blueprint $table) {
            $table->dropForeign(['appointment_id']);
            $table->dropForeign(['updated_by']);
            $table->dropColumn(['appointment_id', 'category', 'template_type', 'record_data', 'is_encrypted', 'updated_by']);
        });
    }
};
