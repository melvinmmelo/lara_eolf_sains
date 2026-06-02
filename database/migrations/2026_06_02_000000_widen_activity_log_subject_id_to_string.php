<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Spatie's activity_log.subject_id ships as an unsigned bigint, which only
     * fits integer-keyed models. Models with a string primary key (e.g.
     * ProductType, whose key is the `code` column like "N3.6L") cannot be
     * audited because writing the string key into a bigint column fails under
     * STRICT_TRANS_TABLES. Widening to a nullable string lets every model be
     * logged; existing integer subject_ids remain valid as strings and the
     * subject_type+subject_id morph lookup still resolves correctly.
     */
    public function up(): void
    {
        Schema::table('activity_log', function (Blueprint $table) {
            $table->string('subject_id', 191)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('activity_log', function (Blueprint $table) {
            $table->unsignedBigInteger('subject_id')->nullable()->change();
        });
    }
};
