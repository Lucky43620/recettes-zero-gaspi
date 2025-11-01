<?php

namespace Database\Seeders;

use App\Models\Ingredient;
use App\Models\Recipe;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Database\Seeder;

class TestDataSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();
        if (!$user) {
            $this->command->error('Aucun utilisateur trouvé. Créez un compte d\'abord.');
            return;
        }

        $units = [
            'g' => Unit::firstOrCreate(['code' => 'g'], ['label' => 'gramme']),
            'ml' => Unit::firstOrCreate(['code' => 'ml'], ['label' => 'millilitre']),
            'pièce' => Unit::firstOrCreate(['code' => 'piece'], ['label' => 'pièce']),
            'cuillère à soupe' => Unit::firstOrCreate(['code' => 'tbsp'], ['label' => 'cuillère à soupe']),
            'cuillère à café' => Unit::firstOrCreate(['code' => 'tsp'], ['label' => 'cuillère à café']),
        ];

        $ingredients = [
            'Tomate' => Ingredient::firstOrCreate(['name' => 'Tomate', 'category' => 'légume']),
            'Oignon' => Ingredient::firstOrCreate(['name' => 'Oignon', 'category' => 'légume']),
            'Ail' => Ingredient::firstOrCreate(['name' => 'Ail', 'category' => 'aromate']),
            'Pâtes' => Ingredient::firstOrCreate(['name' => 'Pâtes', 'category' => 'féculent']),
            'Huile d\'olive' => Ingredient::firstOrCreate(['name' => 'Huile d\'olive', 'category' => 'matière grasse']),
            'Sel' => Ingredient::firstOrCreate(['name' => 'Sel', 'category' => 'assaisonnement']),
            'Poivre' => Ingredient::firstOrCreate(['name' => 'Poivre', 'category' => 'assaisonnement']),
            'Pomme de terre' => Ingredient::firstOrCreate(['name' => 'Pomme de terre', 'category' => 'légume']),
            'Carotte' => Ingredient::firstOrCreate(['name' => 'Carotte', 'category' => 'légume']),
            'Courgette' => Ingredient::firstOrCreate(['name' => 'Courgette', 'category' => 'légume']),
            'Œuf' => Ingredient::firstOrCreate(['name' => 'Œuf', 'category' => 'produit animal']),
            'Lait' => Ingredient::firstOrCreate(['name' => 'Lait', 'category' => 'produit laitier']),
            'Beurre' => Ingredient::firstOrCreate(['name' => 'Beurre', 'category' => 'matière grasse']),
            'Farine' => Ingredient::firstOrCreate(['name' => 'Farine', 'category' => 'féculent']),
            'Fromage râpé' => Ingredient::firstOrCreate(['name' => 'Fromage râpé', 'category' => 'produit laitier']),
        ];

        $recipes = [
            [
                'title' => 'Pâtes à la sauce tomate maison',
                'summary' => 'Un grand classique simple et délicieux, parfait pour utiliser vos tomates.',
                'servings' => 4,
                'prep_minutes' => 10,
                'cook_minutes' => 20,
                'difficulty' => 'easy',
                'is_public' => true,
                'ingredients' => [
                    ['ingredient' => 'Pâtes', 'quantity' => 400, 'unit' => 'g'],
                    ['ingredient' => 'Tomate', 'quantity' => 6, 'unit' => 'pièce'],
                    ['ingredient' => 'Oignon', 'quantity' => 1, 'unit' => 'pièce'],
                    ['ingredient' => 'Ail', 'quantity' => 2, 'unit' => 'pièce'],
                    ['ingredient' => 'Huile d\'olive', 'quantity' => 2, 'unit' => 'cuillère à soupe'],
                    ['ingredient' => 'Sel', 'quantity' => 1, 'unit' => 'cuillère à café'],
                    ['ingredient' => 'Poivre', 'quantity' => 1, 'unit' => 'cuillère à café'],
                ],
                'steps' => [
                    'Faire bouillir une grande casserole d\'eau salée pour les pâtes.',
                    'Pendant ce temps, émincer l\'oignon et l\'ail.',
                    'Faire chauffer l\'huile d\'olive dans une poêle et faire revenir l\'oignon et l\'ail.',
                    'Ajouter les tomates coupées en dés, saler et poivrer.',
                    'Laisser mijoter 15 minutes à feu doux.',
                    'Cuire les pâtes selon les instructions du paquet.',
                    'Égoutter les pâtes et mélanger avec la sauce tomate.',
                ],
            ],
            [
                'title' => 'Gratin de pommes de terre',
                'summary' => 'Gratin crémeux et réconfortant, idéal pour finir vos pommes de terre.',
                'servings' => 6,
                'prep_minutes' => 15,
                'cook_minutes' => 45,
                'difficulty' => 'easy',
                'is_public' => true,
                'ingredients' => [
                    ['ingredient' => 'Pomme de terre', 'quantity' => 1000, 'unit' => 'g'],
                    ['ingredient' => 'Lait', 'quantity' => 400, 'unit' => 'ml'],
                    ['ingredient' => 'Fromage râpé', 'quantity' => 150, 'unit' => 'g'],
                    ['ingredient' => 'Beurre', 'quantity' => 30, 'unit' => 'g'],
                    ['ingredient' => 'Ail', 'quantity' => 2, 'unit' => 'pièce'],
                    ['ingredient' => 'Sel', 'quantity' => 1, 'unit' => 'cuillère à café'],
                    ['ingredient' => 'Poivre', 'quantity' => 1, 'unit' => 'cuillère à café'],
                ],
                'steps' => [
                    'Préchauffer le four à 180°C.',
                    'Éplucher et couper les pommes de terre en fines rondelles.',
                    'Frotter un plat à gratin avec l\'ail coupé en deux.',
                    'Beurrer le plat.',
                    'Disposer les pommes de terre en couches, saler et poivrer entre chaque couche.',
                    'Verser le lait sur les pommes de terre.',
                    'Parsemer de fromage râpé.',
                    'Enfourner pour 45 minutes jusqu\'à ce que le dessus soit doré.',
                ],
            ],
            [
                'title' => 'Tian de légumes',
                'summary' => 'Recette anti-gaspi parfaite pour utiliser vos légumes du garde-manger.',
                'servings' => 4,
                'prep_minutes' => 20,
                'cook_minutes' => 40,
                'difficulty' => 'easy',
                'is_public' => true,
                'ingredients' => [
                    ['ingredient' => 'Tomate', 'quantity' => 3, 'unit' => 'pièce'],
                    ['ingredient' => 'Courgette', 'quantity' => 2, 'unit' => 'pièce'],
                    ['ingredient' => 'Oignon', 'quantity' => 2, 'unit' => 'pièce'],
                    ['ingredient' => 'Huile d\'olive', 'quantity' => 3, 'unit' => 'cuillère à soupe'],
                    ['ingredient' => 'Ail', 'quantity' => 3, 'unit' => 'pièce'],
                    ['ingredient' => 'Sel', 'quantity' => 1, 'unit' => 'cuillère à café'],
                    ['ingredient' => 'Poivre', 'quantity' => 1, 'unit' => 'cuillère à café'],
                ],
                'steps' => [
                    'Préchauffer le four à 180°C.',
                    'Couper tous les légumes en fines rondelles.',
                    'Émincer l\'ail.',
                    'Huiler un plat à gratin.',
                    'Disposer les rondelles de légumes en alternance, debout dans le plat.',
                    'Parsemer d\'ail émincé, arroser d\'huile d\'olive, saler et poivrer.',
                    'Enfourner pour 40 minutes.',
                ],
            ],
            [
                'title' => 'Omelette aux légumes',
                'summary' => 'Recette rapide pour utiliser vos restes de légumes.',
                'servings' => 2,
                'prep_minutes' => 5,
                'cook_minutes' => 10,
                'difficulty' => 'easy',
                'is_public' => true,
                'ingredients' => [
                    ['ingredient' => 'Œuf', 'quantity' => 6, 'unit' => 'pièce'],
                    ['ingredient' => 'Courgette', 'quantity' => 1, 'unit' => 'pièce'],
                    ['ingredient' => 'Oignon', 'quantity' => 1, 'unit' => 'pièce'],
                    ['ingredient' => 'Fromage râpé', 'quantity' => 50, 'unit' => 'g'],
                    ['ingredient' => 'Huile d\'olive', 'quantity' => 2, 'unit' => 'cuillère à soupe'],
                    ['ingredient' => 'Sel', 'quantity' => 1, 'unit' => 'cuillère à café'],
                    ['ingredient' => 'Poivre', 'quantity' => 1, 'unit' => 'cuillère à café'],
                ],
                'steps' => [
                    'Couper les légumes en petits dés.',
                    'Faire chauffer l\'huile dans une poêle.',
                    'Faire revenir les légumes 5 minutes.',
                    'Battre les œufs avec le sel et le poivre.',
                    'Verser les œufs sur les légumes.',
                    'Parsemer de fromage râpé.',
                    'Cuire à feu doux jusqu\'à ce que l\'omelette soit prise.',
                ],
            ],
        ];

        foreach ($recipes as $recipeData) {
            $recipe = Recipe::create([
                'author_id' => $user->id,
                'title' => $recipeData['title'],
                'summary' => $recipeData['summary'],
                'servings' => $recipeData['servings'],
                'prep_minutes' => $recipeData['prep_minutes'],
                'cook_minutes' => $recipeData['cook_minutes'],
                'difficulty' => $recipeData['difficulty'],
                'is_public' => $recipeData['is_public'],
            ]);

            foreach ($recipeData['ingredients'] as $ingredientData) {
                $ingredient = $ingredients[$ingredientData['ingredient']];
                $unit = $ingredientData['unit'] ? $units[$ingredientData['unit']] : null;

                $recipe->ingredients()->attach($ingredient->id, [
                    'quantity' => $ingredientData['quantity'],
                    'unit_code' => $unit?->code,
                ]);
            }

            foreach ($recipeData['steps'] as $index => $instruction) {
                $recipe->steps()->create([
                    'position' => $index + 1,
                    'text' => $instruction,
                ]);
            }

            $this->command->info("✓ Recette créée : {$recipe->title}");
        }

        $this->command->info("\n🎉 Données de test créées avec succès !");
        $this->command->info("📊 " . count($recipes) . " recettes publiques créées");
        $this->command->info("🥕 " . count($ingredients) . " ingrédients disponibles");
    }
}
