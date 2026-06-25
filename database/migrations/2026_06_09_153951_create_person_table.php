<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\Type;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('person', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('surname');
            $table->string('passport')->unique();
            $table->string('email')->unique();
            $table->string('phone')->unique();
            $table->string('brand')->nullable();
            $table->enum('type', array_column(Type::cases(), 'value'))->nullable();

            $table->foreignId('client_portfolio_id')->nullable()->constrained('client_portfolio')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('person');
    }
};
