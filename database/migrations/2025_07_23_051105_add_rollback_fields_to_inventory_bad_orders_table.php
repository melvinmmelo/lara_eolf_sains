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
        Schema::table('inventory_bad_orders', function (Blueprint $table) {
            $table->boolean('is_rolled_back')->default(false)->after('status');
            $table->timestamp('rolled_back_at')->nullable()->after('is_rolled_back');
            $table->unsignedBigInteger('rolled_back_by')->nullable()->after('rolled_back_at');
            $table->text('rollback_reason')->nullable()->after('rolled_back_by');
            
            $table->foreign('rolled_back_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventory_bad_orders', function (Blueprint $table) {
            $table->dropForeign(['rolled_back_by']);
            $table->dropColumn(['is_rolled_back', 'rolled_back_at', 'rolled_back_by', 'rollback_reason']);
        });
    }
};
