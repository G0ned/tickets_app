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
        Schema::table('invitation_list_person', function (Blueprint $table) {
            $table->string('token')->nullable()->unique();
            $table->unsignedInteger('registrations_used')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invitation_list_person', function (Blueprint $table) {
            $table->dropColumn(['token', 'registrations_used']);
        });
    }
};
