<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Records which VerificationCode (if any) was consumed for a given
     * attendee_edition registration, so cancelling it can reactivate that
     * exact code. Nullable: public sign-ups (no invitation) never have one.
     */
    public function up(): void
    {
        Schema::table('attendee_edition', function (Blueprint $table) {
            $table->foreignId('verification_code_id')
                ->nullable()
                ->after('token')
                ->constrained('verification_codes')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('attendee_edition', function (Blueprint $table) {
            $table->dropConstrainedForeignId('verification_code_id');
        });
    }
};
