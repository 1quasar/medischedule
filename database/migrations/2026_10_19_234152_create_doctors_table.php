<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('doctors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('specialty_id')
                ->constrained('specialties')
                ->cascadeOnDelete();
            $table->string('name');
            $table->string('phone', 20);
            $table->time('work_start_time');
            $table->time('work_end_time');
            $table->int('slot_duration');
            $table->boolean('is_active')default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doctors');
    }
};
