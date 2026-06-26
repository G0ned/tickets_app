<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('person', function (Blueprint $table) {
            $table->string('passport')->nullable()->unique()->after('surname');
            $table->string('brand')->nullable()->after('passport');
            $table->enum('type', ['employee', 'client', 'outsider'])->nullable()->after('brand');
        });
    }

    public function down(): void
    {
        Schema::table('person', function (Blueprint $table) {
            $table->dropUnique(['passport']);
            $table->dropColumn(['passport', 'brand', 'type']);
        });
    }
};
