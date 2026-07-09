<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Restores the original RESTRICT behavior for environments that already
     * ran the now-deleted cascade_delete_client_portfolio_on_user_delete migration.
     * Safe to run even on environments that never had the cascade rule applied.
     */
    public function up(): void
    {
        Schema::table('client_portfolio', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    public function down(): void
    {
        Schema::table('client_portfolio', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }
};
