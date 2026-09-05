<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wa_blast_campaigns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wa_device_id')->constrained()->cascadeOnDelete();
            $table->uuid('job_id')->unique();
            $table->text('message');
            $table->string('status')->default('queued');
            $table->unsignedInteger('total')->default(0);
            $table->unsignedInteger('queued')->default(0);
            $table->unsignedInteger('sent')->default(0);
            $table->unsignedInteger('failed')->default(0);
            $table->unsignedInteger('cancelled')->default(0);
            $table->json('phones')->nullable();
            $table->timestamps();

            $table->index(['wa_device_id', 'created_at']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wa_blast_campaigns');
    }
};
