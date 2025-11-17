<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private function indexExists($table, $indexName): bool
    {
        $connection = Schema::getConnection();
        $database = $connection->getDatabaseName();

        $result = DB::select(
            "SELECT COUNT(*) as count FROM information_schema.statistics
             WHERE table_schema = ? AND table_name = ? AND index_name = ?",
            [$database, $table, $indexName]
        );

        return $result[0]->count > 0;
    }

    public function up(): void
    {
        Schema::table('comments', function (Blueprint $table) {
            if (!$this->indexExists('comments', 'comments_recipe_id_index')) {
                $table->index('recipe_id');
            }
        });

        Schema::table('ratings', function (Blueprint $table) {
            if (!$this->indexExists('ratings', 'ratings_recipe_id_index')) {
                $table->index('recipe_id');
            }
        });

        Schema::table('cooksnaps', function (Blueprint $table) {
            if (!$this->indexExists('cooksnaps', 'cooksnaps_recipe_id_index')) {
                $table->index('recipe_id');
            }
        });

        Schema::table('meal_plan_recipes', function (Blueprint $table) {
            if (!$this->indexExists('meal_plan_recipes', 'meal_plan_recipes_meal_plan_id_index')) {
                $table->index('meal_plan_id');
            }
        });

        Schema::table('recipes', function (Blueprint $table) {
            if (!$this->indexExists('recipes', 'recipes_is_public_index')) {
                $table->index('is_public');
            }
            if (!$this->indexExists('recipes', 'recipes_is_public_created_at_index')) {
                $table->index(['is_public', 'created_at']);
            }
            if (!$this->indexExists('recipes', 'recipes_is_public_rating_avg_index')) {
                $table->index(['is_public', 'rating_avg']);
            }
            if (!$this->indexExists('recipes', 'recipes_difficulty_index')) {
                $table->index('difficulty');
            }
        });

        Schema::table('followers', function (Blueprint $table) {
            if (!$this->indexExists('followers', 'followers_following_id_index')) {
                $table->index('following_id');
            }
        });

        Schema::table('recipe_ingredients', function (Blueprint $table) {
            if (!$this->indexExists('recipe_ingredients', 'recipe_ingredients_recipe_id_index')) {
                $table->index('recipe_id');
            }
            if (!$this->indexExists('recipe_ingredients', 'recipe_ingredients_ingredient_id_index')) {
                $table->index('ingredient_id');
            }
        });

        Schema::table('pantry_items', function (Blueprint $table) {
            if (!$this->indexExists('pantry_items', 'pantry_items_ingredient_id_index')) {
                $table->index('ingredient_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('comments', function (Blueprint $table) {
            if ($this->indexExists('comments', 'comments_recipe_id_index')) {
                $table->dropIndex(['recipe_id']);
            }
        });

        Schema::table('ratings', function (Blueprint $table) {
            if ($this->indexExists('ratings', 'ratings_recipe_id_index')) {
                $table->dropIndex(['recipe_id']);
            }
        });

        Schema::table('cooksnaps', function (Blueprint $table) {
            if ($this->indexExists('cooksnaps', 'cooksnaps_recipe_id_index')) {
                $table->dropIndex(['recipe_id']);
            }
        });

        Schema::table('meal_plan_recipes', function (Blueprint $table) {
            if ($this->indexExists('meal_plan_recipes', 'meal_plan_recipes_meal_plan_id_index')) {
                $table->dropIndex(['meal_plan_id']);
            }
        });

        Schema::table('recipes', function (Blueprint $table) {
            if ($this->indexExists('recipes', 'recipes_is_public_index')) {
                $table->dropIndex(['is_public']);
            }
            if ($this->indexExists('recipes', 'recipes_is_public_created_at_index')) {
                $table->dropIndex(['is_public', 'created_at']);
            }
            if ($this->indexExists('recipes', 'recipes_is_public_rating_avg_index')) {
                $table->dropIndex(['is_public', 'rating_avg']);
            }
            if ($this->indexExists('recipes', 'recipes_difficulty_index')) {
                $table->dropIndex(['difficulty']);
            }
        });

        Schema::table('followers', function (Blueprint $table) {
            if ($this->indexExists('followers', 'followers_following_id_index')) {
                $table->dropIndex(['following_id']);
            }
        });

        Schema::table('recipe_ingredients', function (Blueprint $table) {
            if ($this->indexExists('recipe_ingredients', 'recipe_ingredients_recipe_id_index')) {
                $table->dropIndex(['recipe_id']);
            }
            if ($this->indexExists('recipe_ingredients', 'recipe_ingredients_ingredient_id_index')) {
                $table->dropIndex(['ingredient_id']);
            }
        });

        Schema::table('pantry_items', function (Blueprint $table) {
            if ($this->indexExists('pantry_items', 'pantry_items_ingredient_id_index')) {
                $table->dropIndex(['ingredient_id']);
            }
        });
    }
};
