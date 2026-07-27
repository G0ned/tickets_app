<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Backfills columns that exist on the live `person` table (passport, brand,
     * type) but were never captured in a migration, so fresh databases (CI, new
     * clones, first prod deploy) match what's actually running today.
     *
     * Every step is guarded with an existence check: some environments (this
     * one included) already have these columns/constraint from a manual ALTER
     * done before this migration existed, and re-running the bare ALTER there
     * throws "column/constraint already exists" and crash-loops the app on
     * boot (this project's entrypoint runs migrations on every container
     * start). The guards make it safe to apply on both drifted and fresh DBs.
     *
     * The `type` check constraint is Postgres-only: SQLite (used in tests) can't
     * ADD CONSTRAINT via ALTER TABLE, and App\Enums\Type already enforces valid
     * values at the application layer.
     */
    public function up(): void
    {
        Schema::table('person', function (Blueprint $table) {
            if (!Schema::hasColumn('person', 'passport')) {
                $table->string('passport')->unique()->after('surname');
            }
            if (!Schema::hasColumn('person', 'brand')) {
                $table->string('brand')->nullable()->after('phone');
            }
            if (!Schema::hasColumn('person', 'type')) {
                $table->string('type')->nullable()->after('brand');
            }
        });

        if (DB::connection()->getDriverName() === 'pgsql') {
            $constraintExists = DB::selectOne(
                "SELECT 1 FROM pg_constraint WHERE conname = 'person_type_check'"
            );

            if (!$constraintExists) {
                DB::statement("ALTER TABLE person ADD CONSTRAINT person_type_check CHECK (type IN ('employee', 'client', 'outsider'))");
            }
        }
    }

    public function down(): void
    {
        if (DB::connection()->getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE person DROP CONSTRAINT IF EXISTS person_type_check');
        }

        Schema::table('person', function (Blueprint $table) {
            $existing = array_filter(
                ['passport', 'brand', 'type'],
                fn ($column) => Schema::hasColumn('person', $column)
            );

            if ($existing !== []) {
                $table->dropColumn($existing);
            }
        });
    }
};
