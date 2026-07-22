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
            $table->unsignedInteger('allowed_registrations')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invitation_list_person', function (Blueprint $table) {
            $table->dropColumn('allowed_registrations');
        });
    }
};
