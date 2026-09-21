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
        Schema::table('stock_batches', function (Blueprint $table) {
            $table->enum('status', ['pending', 'received', 'cancelled'])->default('received')->after('supplier');
            $table->foreignId('received_by')->nullable()->constrained('users')->nullOnDelete()->after('status');
            $table->timestamp('confirmed_at')->nullable()->after('received_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_batches', function (Blueprint $table) {
            $table->dropForeign(['received_by']);
            $table->dropColumn(['status', 'received_by', 'confirmed_at']);
        });
    }
};
