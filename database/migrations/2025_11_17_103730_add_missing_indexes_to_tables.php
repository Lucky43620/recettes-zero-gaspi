<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('comments', function (Blueprint $table) {
            $table->index('recipe_id');
        });

        Schema::table('ratings', function (Blueprint $table) {
            $table->index('recipe_id');
        });

        Schema::table('cooksnaps', function (Blueprint $table) {
            $table->index('recipe_id');
        });

        Schema::table('meal_plan_recipes', function (Blueprint $table) {
            $table->index('meal_plan_id');
        });
    }

    public function down(): void
    {
        Schema::table('comments', function (Blueprint $table) {
            $table->dropIndex(['recipe_id']);
        });

        Schema::table('ratings', function (Blueprint $table) {
            $table->dropIndex(['recipe_id']);
        });

        Schema::table('cooksnaps', function (Blueprint $table) {
            $table->dropIndex(['recipe_id']);
        });

        Schema::table('meal_plan_recipes', function (Blueprint $table) {
            $table->dropIndex(['meal_plan_id']);
        });
    }
};
