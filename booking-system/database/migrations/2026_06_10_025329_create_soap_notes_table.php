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
        Schema::create('soap_notes', function (Blueprint $table) {
            $table->id();

            $table->foreignId('appointment_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('staff_id')
                ->nullable()
                ->constrained('staff')
                ->nullOnDelete();

            $table->longText('subjective')->nullable();
            $table->longText('objective')->nullable();
            $table->longText('assessment')->nullable();
            $table->longText('plan')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('soap_notes');
    }
};
