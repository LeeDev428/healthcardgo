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
        Schema::create('diseases', function (Blueprint $table) {
            $table->id();
            $table->string('disease_type'); // HIV, Measles, Dengue, etc.
            $table->string('case_number')->unique();
            $table->string('status')->default('suspected');
            $table->date('onset_date')->nullable();
            $table->date('reported_date');
            $table->date('confirmed_date')->nullable();
            $table->foreignId('barangay_id')->nullable()->constrained()->onDelete('set null');
            $table->json('symptoms')->nullable();
            $table->text('risk_factors')->nullable();
            $table->text('treatment_notes')->nullable();
            $table->foreignId('reported_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('diseases');
    }
};
