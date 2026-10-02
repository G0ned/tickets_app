<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendee_edition_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('edition_id')->constrained('editions');
            $table->foreignId('attendee_id')->constrained('person');
            $table->string('token')->nullable();
            $table->boolean('auth_for_ad')->default(false);
            $table->boolean('auth_for_comms')->default(false);
            $table->boolean('auth_image_rights')->default(false);
            $table->boolean('privacy_policy')->default(false);
            $table->boolean('attendance')->default(false);
            $table->dateTime('checked_in_at')->nullable();
            $table->foreignId('verification_code_id')->nullable()->constrained('verification_codes')->nullOnDelete();
            $table->boolean('is_guest')->default(false);
            // The original attendee_edition.created_at (when they actually
            // registered), distinct from archived_at (when this snapshot
            // was taken, i.e. when the edition got reactivated).
            $table->timestamp('registered_at')->nullable();
            $table->timestamp('archived_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendee_edition_history');
    }
};
