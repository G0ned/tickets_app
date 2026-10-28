<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Restores the unique constraint that 2026_06_26_000001_drop_unique_constraints_from_person_table.php
     * removed. That migration was a workaround for a real bug (Person::create()
     * crashing with a raw DB exception when a brand-new passport happened to
     * share an email/phone with a different, unrelated person) rather than a
     * deliberate business rule - the business actually wants one real email per
     * attendee (they're potential leads). The crash itself is now handled
     * properly with a friendly error in AttendeeRegistrationService::register(),
     * so the constraint can come back.
     */
    public function up(): void
    {
        $this->disambiguateDuplicates('email');
        $this->disambiguateDuplicates('phone');

        Schema::table('person', function (Blueprint $table) {
            $table->unique('email');
            $table->unique('phone');
        });
    }

    public function down(): void
    {
        Schema::table('person', function (Blueprint $table) {
            $table->dropUnique(['email']);
            $table->dropUnique(['phone']);
        });
    }

    /**
     * A unique index can't be created while duplicates already exist, and this
     * runs in every environment (including ones with real, pre-existing data
     * this migration's author can't inspect), so duplicates are resolved
     * programmatically rather than assumed away:
     * the oldest row (lowest id) for a given value keeps it untouched; every
     * later row sharing it gets a clearly-marked, still-unique placeholder
     * instead, so the migration never fails and the affected rows stay easy to
     * find and fix by hand afterwards (they need their real, distinct value).
     */
    private function disambiguateDuplicates(string $column): void
    {
        $duplicateValues = DB::table('person')
            ->select($column)
            ->whereNotNull($column)
            ->groupBy($column)
            ->havingRaw('count(*) > 1')
            ->pluck($column);

        foreach ($duplicateValues as $value) {
            $ids = DB::table('person')->where($column, $value)->orderBy('id')->pluck('id');

            foreach ($ids->slice(1) as $id) {
                $placeholder = $column === 'email'
                    ? preg_replace('/@/', "+legacydup{$id}@", $value, 1)
                    : "{$value}-legacydup{$id}";

                DB::table('person')->where('id', $id)->update([$column => $placeholder]);
            }
        }
    }
};
