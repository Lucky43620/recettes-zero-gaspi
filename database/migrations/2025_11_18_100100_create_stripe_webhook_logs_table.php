<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stripe_webhook_logs', function (Blueprint $table) {
            $table->id();
            $table->string('event_id')->unique();
            $table->string('event_type');
            $table->json('payload');
            $table->timestamp('processed_at')->nullable();
            $table->enum('status', ['pending', 'success', 'failed'])->default('pending');
            $table->text('error_message')->nullable();
            $table->timestamps();

            $table->index('event_type');
            $table->index('status');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stripe_webhook_logs');
    }
};
