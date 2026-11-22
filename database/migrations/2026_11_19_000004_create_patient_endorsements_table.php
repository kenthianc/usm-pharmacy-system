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
        Schema::create('patient_endorsements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admission_id')->constrained('admissions')->cascadeOnDelete();
            $table->foreignId('endorsed_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('receiving_nurse_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('nurse_station');
            $table->text('endorsement_notes');
            $table->enum('acknowledgment_status', ['pending', 'acknowledged'])->default('pending')->index();
            $table->dateTime('acknowledged_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patient_endorsements');
    }
};
