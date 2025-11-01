<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Recipe;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CommentSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Comment::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $users = User::all();
        $recipes = Recipe::where('is_public', true)->limit(30)->get();

        $comments = [
            'Excellente recette, merci pour le partage !',
            'Très facile à réaliser, même pour un débutant.',
            'Ma famille a adoré, je referai sans hésiter.',
            'Un vrai régal, merci chef !',
            'Parfait pour utiliser mes restes de légumes.',
            'Recette testée et approuvée, bravo !',
            'Simple et délicieux, que demander de plus ?',
            'Un classique revisité avec brio.',
            'Idéal pour un repas de semaine rapide.',
            'Les enfants ont tout mangé, c\'est dire !',
        ];

        foreach ($recipes as $recipe) {
            $commentCount = rand(1, 5);
            for ($i = 0; $i < $commentCount; $i++) {
                Comment::create([
                    'user_id' => $users->random()->id,
                    'recipe_id' => $recipe->id,
                    'content' => $comments[array_rand($comments)],
                    'upvotes' => rand(0, 20),
                    'downvotes' => rand(0, 3),
                    'created_at' => now()->subDays(rand(1, 180)),
                ]);
            }
        }
    }
}
