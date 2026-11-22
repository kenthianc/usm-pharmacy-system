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
        Schema::table('prescriptions', function (Blueprint $table) {
            $table->string('order_type')->default('outpatient')->after('doctor_name');
            $table->string('room_bed_number')->nullable()->after('order_type');
            $table->string('billing_status')->default('pending_payment')->after('status');
        });

        Schema::table('pos_transactions', function (Blueprint $table) {
            $table->decimal('subtotal', 10, 2)->nullable()->after('cashier_id');
            $table->string('discount_type')->default('regular')->after('total_amount');
            $table->string('discount_id_number')->nullable()->after('discount_type');
            $table->decimal('vat_exempt_amount', 10, 2)->default(0)->after('discount_id_number');
            $table->decimal('discount_amount', 10, 2)->default(0)->after('vat_exempt_amount');
            $table->decimal('net_amount', 10, 2)->nullable()->after('discount_amount');
            $table->string('order_type')->default('outpatient')->after('payment_method');
            $table->string('room_bed_number')->nullable()->after('order_type');
            $table->string('billing_status')->default('paid')->after('room_bed_number');
        });

        Schema::create('patient_bills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('patients')->cascadeOnDelete();
            $table->foreignId('prescription_id')->nullable()->constrained('prescriptions')->nullOnDelete();
            $table->foreignId('pos_transaction_id')->nullable()->constrained('pos_transactions')->nullOnDelete();
            $table->string('room_bed_number')->nullable();
            $table->string('doctor_name')->nullable();
            $table->decimal('gross_amount', 10, 2);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('net_amount', 10, 2);
            $table->string('status')->default('billed_to_account')->index();
            $table->foreignId('billed_by')->constrained('users')->cascadeOnDelete();
            $table->timestamp('settled_at')->nullable();
            $table->foreignId('settled_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patient_bills');

        Schema::table('pos_transactions', function (Blueprint $table) {
            $table->dropColumn([
                'subtotal',
                'discount_type',
                'discount_id_number',
                'vat_exempt_amount',
                'discount_amount',
                'net_amount',
                'order_type',
                'room_bed_number',
                'billing_status',
            ]);
        });

        Schema::table('prescriptions', function (Blueprint $table) {
            $table->dropColumn([
                'order_type',
                'room_bed_number',
                'billing_status',
            ]);
        });
    }
};
