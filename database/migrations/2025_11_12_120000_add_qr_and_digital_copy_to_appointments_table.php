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
        Schema::table('appointments', function (Blueprint $table) {
            if (! Schema::hasColumn('appointments', 'qr_code_path')) {
                $table->string('qr_code_path')->nullable()->after('fee');
            }

            if (! Schema::hasColumn('appointments', 'digital_copy_path')) {
                $table->string('digital_copy_path')->nullable()->after('qr_code_path');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            if (Schema::hasColumn('appointments', 'digital_copy_path')) {
                $table->dropColumn('digital_copy_path');
            }

            if (Schema::hasColumn('appointments', 'qr_code_path')) {
                $table->dropColumn('qr_code_path');
            }
        });
    }
};
