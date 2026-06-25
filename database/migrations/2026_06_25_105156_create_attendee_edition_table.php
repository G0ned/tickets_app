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
        Schema::create('attendee_edition', function (Blueprint $table) {
            $table->foreignId('edition_id')->constrained('editions');
            $table->foreignId('attendee_id')->constrained('person');
            $table->timestamps();
            $table->boolean('auth_for_ad')->default(false);
            $table->boolean('auth_for_comms')->default(false);
            $table->boolean('auth_image_rights')->default(false);
            $table->boolean('privacy_policy')->default(false);
            $table->boolean('attendance')->default(false);
            $table->dateTime('checked_in_at')->nullable();

            $table->primary(['edition_id', 'attendee_id']);
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendee_edition');
    }
};
