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
        Schema::create('attendees', function (Blueprint $table) {
            $table->string('user_id', 9)->primary();
            $table->foreign('user_id')
                  ->references('identification')
                  ->on('users')
                  ->onDelete('cascade');
            $table->string('phone')->nullable();
            $table->string('zip_code')->nullable();
            $table->boolean('privacy_policy');
            $table->boolean('img_rights_ads');
            $table->boolean('img_rights_web');
            $table->boolean('img_rights_rss');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendees');
    }
};
