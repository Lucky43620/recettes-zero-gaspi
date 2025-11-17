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

        Schema::table('recipes', function (Blueprint $table) {
            $table->index('is_public');
            $table->index(['is_public', 'created_at']);
            $table->index(['is_public', 'rating_avg']);
            $table->index('difficulty');
        });

        Schema::table('followers', function (Blueprint $table) {
            $table->index('following_id');
        });

        Schema::table('recipe_ingredients', function (Blueprint $table) {
            $table->index('recipe_id');
            $table->index('ingredient_id');
            $table->index('position');
        });

        Schema::table('pantry_items', function (Blueprint $table) {
            $table->index('ingredient_id');
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

        Schema::table('recipes', function (Blueprint $table) {
            $table->dropIndex(['is_public']);
            $table->dropIndex(['is_public', 'created_at']);
            $table->dropIndex(['is_public', 'rating_avg']);
            $table->dropIndex(['difficulty']);
        });

        Schema::table('followers', function (Blueprint $table) {
            $table->dropIndex(['following_id']);
        });

        Schema::table('recipe_ingredients', function (Blueprint $table) {
            $table->dropIndex(['recipe_id']);
            $table->dropIndex(['ingredient_id']);
            $table->dropIndex(['position']);
        });

        Schema::table('pantry_items', function (Blueprint $table) {
            $table->dropIndex(['ingredient_id']);
        });
    }
};
