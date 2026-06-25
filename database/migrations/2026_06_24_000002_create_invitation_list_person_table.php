<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invitation_list_person', function (Blueprint $table) {
            $table->foreignId('invitation_list_id')
                ->constrained('invitation_lists')
                ->cascadeOnDelete();

            $table->foreignId('person_id')
                ->constrained('person')
                ->cascadeOnDelete();

            $table->primary(['invitation_list_id', 'person_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invitation_list_person');
    }
};
