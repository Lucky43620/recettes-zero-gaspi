<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('deleted_users_audit', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('original_user_id');
            $table->timestamp('deletion_date');
            $table->string('stripe_customer_id')->nullable();
            $table->decimal('total_spent', 10, 2)->default(0);
            $table->json('subscription_history')->nullable();
            $table->date('legal_retention_until');
            $table->string('deleted_by')->nullable();
            $table->timestamps();

            $table->index('deletion_date');
            $table->index('legal_retention_until');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deleted_users_audit');
    }
};
