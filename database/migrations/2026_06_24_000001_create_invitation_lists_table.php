<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invitation_lists', function (Blueprint $table) {
            $table->id();
            $table->string('name');

            $table->foreignId('client_portfolio_id')
                ->constrained('client_portfolio')
                ->cascadeOnDelete();
            $table->unsignedBigInteger('user_edition_id')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invitation_lists');
    }
};
