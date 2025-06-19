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
        //
        Schema::table('equipment_store', function (Blueprint $table) {
            $table->string('notes_free_small_cup')->nullable();
            $table->string('loader_name')->nullable();
            $table->string('checker_name')->nullable();
            $table->string('remarks_gatepass')->nullable();
            $table->boolean('has_ice_scraper')->nullable();
            $table->boolean('has_lock_and_key')->nullable();
            $table->boolean('has_signage_bracket')->nullable();
            $table->boolean('has_tarpaulin_logo')->nullable();
            $table->boolean('has_tarpaulin_pricelist')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::table('equipment_store', function (Blueprint $table) {
            $table->dropColumn('notes_free_small_cup');
            $table->dropColumn('loader_name');
            $table->dropColumn('checker_name');
            $table->dropColumn('remarks_gatepass');
            $table->dropColumn('has_ice_scraper');
            $table->dropColumn('has_lock_and_key');
            $table->dropColumn('has_signage_bracket');
            $table->dropColumn('has_tarpaulin_logo');
            $table->dropColumn('has_tarpaulin_pricelist');
        });
    }
};
