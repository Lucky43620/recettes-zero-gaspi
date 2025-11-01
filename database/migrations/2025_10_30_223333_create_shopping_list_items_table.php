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
        Schema::create('shopping_list_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shopping_list_id')->constrained()->onDelete('cascade');
            $table->foreignId('ingredient_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('name')->nullable();
            $table->decimal('quantity', 10, 2)->nullable();
            $table->string('unit_code', 20)->nullable();
            $table->boolean('is_checked')->default(false);
            $table->timestamps();

            $table->foreign('unit_code')->references('code')->on('units')->onDelete('set null');
            $table->index(['shopping_list_id', 'is_checked']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shopping_list_items');
    }
};
