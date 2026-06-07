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
        Schema::create('staff_working_hours', function (Blueprint $table) {
            $table->id();

            $table->foreignId('staff_id')
                ->constrained('staff')
                ->cascadeOnDelete();

            $table->string('schedule_type')->default('weekly');
            // weekly, date_range, specific_date

            $table->tinyInteger('day_of_week')->nullable();
            // 0 = Sunday, 1 = Monday ... 6 = Saturday

            $table->date('specific_date')->nullable();

            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();

            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();

            $table->boolean('is_available')->default(true);

            $table->timestamps();

            $table->index('staff_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staff_working_hours');
    }
};
