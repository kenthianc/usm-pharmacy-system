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
        Schema::table('medicines', function (Blueprint $table) {
            $table->string('code', 50)->nullable()->unique()->after('id');
            $table->string('barcode', 100)->nullable()->after('code');
            $table->decimal('stockout_risk_score', 5, 2)->nullable()->after('reorder_level');
            $table->string('stockout_risk_category', 20)->nullable()->after('stockout_risk_score');
            $table->decimal('daily_consumption_rate', 8, 2)->default(0.00)->after('stockout_risk_category');
        });

        Schema::table('stock_batches', function (Blueprint $table) {
            $table->decimal('expiry_risk_score', 5, 2)->nullable()->after('quantity_remaining');
            $table->string('expiry_risk_category', 20)->nullable()->after('expiry_risk_score');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_batches', function (Blueprint $table) {
            $table->dropColumn(['expiry_risk_score', 'expiry_risk_category']);
        });

        Schema::table('medicines', function (Blueprint $table) {
            $table->dropColumn(['code', 'barcode', 'stockout_risk_score', 'stockout_risk_category', 'daily_consumption_rate']);
        });
    }
};
