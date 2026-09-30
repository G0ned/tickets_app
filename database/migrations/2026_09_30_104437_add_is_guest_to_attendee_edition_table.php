<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Flags a registration made by using someone else's verification code
     * (an invited client's own invitation link) when the person who actually
     * registered isn't themselves already a known client (Person::type !==
     * Type::Client) - i.e. a genuinely external guest the client brought,
     * not the client using their own code for themselves. Lives per
     * registration (this table), not on `person`, since it's a fact about
     * this specific sign-up (which verification code, if any, was used),
     * the same way `verification_code_id` itself already does - see
     * InvitationRegistrationController::store(), the only place this gets
     * set to true. Defaults to false: a public sign-up (FormController,
     * no verification code involved at all) is never a "guest".
     */
    public function up(): void
    {
        Schema::table('attendee_edition', function (Blueprint $table) {
            $table->boolean('is_guest')->default(false)->after('verification_code_id');
        });
    }

    public function down(): void
    {
        Schema::table('attendee_edition', function (Blueprint $table) {
            $table->dropColumn('is_guest');
        });
    }
};
