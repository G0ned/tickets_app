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
        Schema::table('invitation_lists', function (Blueprint $table) {
            $table->dropColumn('user_edition_id');

            $table->foreignId('edition_id')
                ->nullable()
                ->constrained('editions')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invitation_lists', function (Blueprint $table) {
            $table->dropForeign(['edition_id']);
            $table->dropColumn('edition_id');

            $table->unsignedBigInteger('user_edition_id')->nullable();
        });
    }
};
