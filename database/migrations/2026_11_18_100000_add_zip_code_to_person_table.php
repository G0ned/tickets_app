<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Guarded because some environments already have this column (added
     * outside the migration history); skip instead of failing there.
     */
    public function up(): void
    {
        if (Schema::hasColumn('person', 'zip_code')) {
            return;
        }

        Schema::table('person', function (Blueprint $table) {
            $table->string('zip_code', 5)->nullable()->after('phone');
        });
    }

    public function down(): void
    {
        // Intentionally a no-op: in environments where the column pre-existed,
        // this migration doesn't own it, so rolling back must not drop it.
    }
};
