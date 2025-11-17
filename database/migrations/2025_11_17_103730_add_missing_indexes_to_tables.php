<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('comments', function (Blueprint $table) {
            if (!$table->hasIndex(['recipe_id'], true)) {
                $table->index('recipe_id');
            }
        });

        Schema::table('ratings', function (Blueprint $table) {
            if (!$table->hasIndex(['recipe_id'], true)) {
                $table->index('recipe_id');
            }
        });

        Schema::table('cooksnaps', function (Blueprint $table) {
            if (!$table->hasIndex(['recipe_id'], true)) {
                $table->index('recipe_id');
            }
        });

        Schema::table('meal_plan_recipes', function (Blueprint $table) {
            if (!$table->hasIndex(['meal_plan_id'], true)) {
                $table->index('meal_plan_id');
            }
        });

        Schema::table('recipes', function (Blueprint $table) {
            if (!$table->hasIndex(['is_public'], true)) {
                $table->index('is_public');
            }
            if (!$table->hasIndex(['is_public', 'created_at'], true)) {
                $table->index(['is_public', 'created_at']);
            }
            if (!$table->hasIndex(['is_public', 'rating_avg'], true)) {
                $table->index(['is_public', 'rating_avg']);
            }
            if (!$table->hasIndex(['difficulty'], true)) {
                $table->index('difficulty');
            }
        });

        Schema::table('followers', function (Blueprint $table) {
            if (!$table->hasIndex(['following_id'], true)) {
                $table->index('following_id');
            }
        });

        Schema::table('recipe_ingredients', function (Blueprint $table) {
            if (!$table->hasIndex(['recipe_id'], true)) {
                $table->index('recipe_id');
            }
            if (!$table->hasIndex(['ingredient_id'], true)) {
                $table->index('ingredient_id');
            }
            if (!$table->hasIndex(['position'], true)) {
                $table->index('position');
            }
        });

        Schema::table('pantry_items', function (Blueprint $table) {
            if (!$table->hasIndex(['ingredient_id'], true)) {
                $table->index('ingredient_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('comments', function (Blueprint $table) {
            if ($table->hasIndex(['recipe_id'], true)) {
                $table->dropIndex(['recipe_id']);
            }
        });

        Schema::table('ratings', function (Blueprint $table) {
            if ($table->hasIndex(['recipe_id'], true)) {
                $table->dropIndex(['recipe_id']);
            }
        });

        Schema::table('cooksnaps', function (Blueprint $table) {
            if ($table->hasIndex(['recipe_id'], true)) {
                $table->dropIndex(['recipe_id']);
            }
        });

        Schema::table('meal_plan_recipes', function (Blueprint $table) {
            if ($table->hasIndex(['meal_plan_id'], true)) {
                $table->dropIndex(['meal_plan_id']);
            }
        });

        Schema::table('recipes', function (Blueprint $table) {
            if ($table->hasIndex(['is_public'], true)) {
                $table->dropIndex(['is_public']);
            }
            if ($table->hasIndex(['is_public', 'created_at'], true)) {
                $table->dropIndex(['is_public', 'created_at']);
            }
            if ($table->hasIndex(['is_public', 'rating_avg'], true)) {
                $table->dropIndex(['is_public', 'rating_avg']);
            }
            if ($table->hasIndex(['difficulty'], true)) {
                $table->dropIndex(['difficulty']);
            }
        });

        Schema::table('followers', function (Blueprint $table) {
            if ($table->hasIndex(['following_id'], true)) {
                $table->dropIndex(['following_id']);
            }
        });

        Schema::table('recipe_ingredients', function (Blueprint $table) {
            if ($table->hasIndex(['recipe_id'], true)) {
                $table->dropIndex(['recipe_id']);
            }
            if ($table->hasIndex(['ingredient_id'], true)) {
                $table->dropIndex(['ingredient_id']);
            }
            if ($table->hasIndex(['position'], true)) {
                $table->dropIndex(['position']);
            }
        });

        Schema::table('pantry_items', function (Blueprint $table) {
            if ($table->hasIndex(['ingredient_id'], true)) {
                $table->dropIndex(['ingredient_id']);
            }
        });
    }
};
