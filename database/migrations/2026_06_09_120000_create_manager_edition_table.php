<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('manager_edition', function (Blueprint $table) {
            $table->foreignId('edition_id')->constrained('editions')->cascadeOnDelete();
            $table->foreignId('manager_id')->constrained('users')->cascadeOnDelete();
            $table->boolean('is_supervisor')->default(false);
            $table->boolean('is_doorman')->default(false);
            $table->integer('invitations_capacity')->nullable();
            $table->primary(['edition_id', 'manager_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('manager_edition');
    }
};
