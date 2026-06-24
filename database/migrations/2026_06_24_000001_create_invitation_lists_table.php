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

            // The portfolio whose contacts can be invited.
            $table->foreignId('client_portfolio_id')
                ->constrained('client_portfolio')
                ->cascadeOnDelete();

            // Intended to reference a manager_edition row, but that table uses a composite
            // primary key (no auto-increment id), so we store the value as a plain nullable
            // integer until the manager_edition table gains a surrogate key.
            $table->unsignedBigInteger('user_edition_id')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invitation_lists');
    }
};
