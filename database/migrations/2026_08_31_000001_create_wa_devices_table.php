<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wa_devices', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('session')->unique();
            $table->string('phone')->nullable();
            $table->string('status')->default('disconnected');
            $table->text('token')->nullable();
            $table->longText('qrcode')->nullable();
            $table->string('session_status')->nullable();
            $table->timestamp('connected_at')->nullable();
            $table->timestamp('last_seen_at')->nullable();
            $table->timestamps();

            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wa_devices');
    }
};
