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
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['patient', 'doctor', 'employee']);
            $table->string('patient_id')->nullable()->unique();
            $table->string('doctor_id')->nullable()->unique();
            $table->string('employee_id')->nullable()->unique();
            $table->string('phone')->nullable();
            $table->string('name')->nullable();
            $table->dateTime('checkup')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
