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
        Schema::table('pos_transactions', function (Blueprint $table) {
            $table->string('collected_by')->nullable()->after('billing_status');
        });

        Schema::table('prescriptions', function (Blueprint $table) {
            $table->string('collected_by')->nullable()->after('billing_status');
        });

        Schema::table('patients', function (Blueprint $table) {
            $table->string('patient_type', 50)->default('student')->change();
            $table->string('emergency_contact')->nullable()->after('contact_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->dropColumn('emergency_contact');
        });

        Schema::table('prescriptions', function (Blueprint $table) {
            $table->dropColumn('collected_by');
        });

        Schema::table('pos_transactions', function (Blueprint $table) {
            $table->dropColumn('collected_by');
        });
    }
};
