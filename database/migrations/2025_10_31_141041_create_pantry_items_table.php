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
        Schema::create('pantry_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('ingredient_id')->constrained()->onDelete('cascade');
            $table->decimal('quantity', 10, 2);
            $table->string('unit_code', 20);
            $table->date('expiration_date')->nullable();
            $table->string('storage_location')->nullable();
            $table->boolean('opened')->default(false);
            $table->timestamps();

            $table->foreign('unit_code')->references('code')->on('units')->onDelete('cascade');
            $table->index(['user_id', 'expiration_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pantry_items');
    }
};
