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
        Schema::table('events', function (Blueprint $table) {
            // One link shared by every edition of this event, by explicit request -
            // the client doesn't want to generate a separate satisfaction survey per
            // edition. If that ever needs to be per-edition instead, this is the
            // column to move to the editions table.
            $table->string('survey_url')->nullable()->after('poster_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn('survey_url');
        });
    }
};
