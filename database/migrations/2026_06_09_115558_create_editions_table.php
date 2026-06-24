<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Event;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('editions', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Event::class, 'event_id');
            $table->dateTime('date');
            $table->float('duration', precision: 2);
            $table->string('location');
            $table->integer('capacity');
            $table->boolean('status');
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['date', 'location']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('editions');
    }
};
