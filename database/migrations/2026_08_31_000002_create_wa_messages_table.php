<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wa_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wa_device_id')->constrained()->cascadeOnDelete();
            $table->string('wa_message_id')->nullable();
            $table->string('direction');
            $table->string('from_number')->nullable();
            $table->string('to_number')->nullable();
            $table->text('body')->nullable();
            $table->string('type')->default('chat');
            $table->string('status')->nullable();
            $table->string('notify_name')->nullable();
            $table->boolean('is_group')->default(false);
            $table->json('raw_payload')->nullable();
            $table->timestamps();

            $table->index(['wa_device_id', 'created_at']);
            $table->index('wa_message_id');
            $table->index('direction');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wa_messages');
    }
};
