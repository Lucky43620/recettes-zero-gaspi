<?php

namespace Database\Seeders;

use App\Models\Ingredient;
use App\Models\Recipe;
use App\Models\RecipeStep;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RealRecipeSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Recipe::truncate();
        DB::table('recipe_steps')->truncate();
        DB::table('recipe_ingredients')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $users = User::all();
        if ($users->isEmpty()) {
            $this->command->error('Aucun utilisateur trouvé. Veuillez d\'abord exécuter UserSeeder.');
            return;
        }

        $recipes = $this->getRealRecipes();

        foreach ($recipes as $recipeData) {
            $recipe = Recipe::create([
                'author_id' => $users->random()->id,
                'title' => $recipeData['title'],
                'summary' => $recipeData['summary'],
                'servings' => $recipeData['servings'],
                'prep_minutes' => $recipeData['prep_minutes'],
                'cook_minutes' => $recipeData['cook_minutes'],
                'difficulty' => $recipeData['difficulty'],
                'cuisine' => $recipeData['cuisine'],
                'is_public' => true,
                'calories' => $recipeData['calories'] ?? null,
            ]);

            foreach ($recipeData['steps'] as $index => $stepData) {
                RecipeStep::create([
                    'recipe_id' => $recipe->id,
                    'position' => $index + 1,
                    'text' => $stepData['text'],
                    'timer_minutes' => $stepData['timer'] ?? null,
                ]);
            }

            if (isset($recipeData['ingredients'])) {
                foreach ($recipeData['ingredients'] as $ingredientData) {
                    $ingredient = Ingredient::firstOrCreate(
                        ['name' => $ingredientData['name']],
                        ['category' => $ingredientData['category'] ?? 'Autre']
                    );

                    $unit = null;
                    if (isset($ingredientData['unit'])) {
                        $unitCode = $this->mapUnitToCode($ingredientData['unit']);
                        $unit = Unit::where('code', $unitCode)->first();
                    }

                    $recipe->ingredients()->attach($ingredient->id, [
                        'quantity' => $ingredientData['quantity'] ?? null,
                        'unit_id' => $unit?->id,
                    ]);
                }
            }
        }

        $this->command->info('✅ ' . count($recipes) . ' recettes réalistes créées avec succès !');
    }

    private function mapUnitToCode(string $unit): string
    {
        $mapping = [
            'g' => 'g',
            'kg' => 'kg',
            'ml' => 'ml',
            'l' => 'l',
            'pièce' => 'piece',
            'tranches' => 'slice',
            'c. à soupe' => 'tbsp',
            'c. à café' => 'tsp',
            'gousses' => 'clove',
            'branches' => 'branch',
            'feuilles' => 'leaf',
            'pot' => 'jar',
            'pots' => 'jar',
            'sachet' => 'sachet',
            'rouleau' => 'roll',
            'pincée' => 'pinch',
            'grains' => 'grain',
            'gousse ou c. à café' => 'piece',
        ];

        return $mapping[$unit] ?? $unit;
    }

    private function getRealRecipes(): array
    {
        return [
            [
                'title' => 'Pain perdu aux fruits abîmés',
                'summary' => 'Donnez une seconde vie à votre pain rassis et vos fruits trop mûrs avec cette recette gourmande et anti-gaspi.',
                'servings' => 4,
                'prep_minutes' => 10,
                'cook_minutes' => 15,
                'difficulty' => 'easy',
                'cuisine' => 'Française',
                'calories' => 320,
                'steps' => [
                    ['text' => 'Dans un saladier, battez 3 œufs avec 200 ml de lait et 2 cuillères à soupe de sucre.'],
                    ['text' => 'Coupez le pain rassis en tranches épaisses d\'environ 2 cm.'],
                    ['text' => 'Trempez chaque tranche de pain dans le mélange œuf-lait pendant 20 secondes de chaque côté.'],
                    ['text' => 'Faites chauffer une poêle avec un peu de beurre à feu moyen.'],
                    ['text' => 'Faites dorer les tranches de pain 3-4 minutes de chaque côté jusqu\'à ce qu\'elles soient bien dorées.', 'timer' => 8],
                    ['text' => 'Pendant ce temps, coupez vos fruits abîmés en morceaux en retirant les parties vraiment trop abîmées.'],
                    ['text' => 'Faites revenir les fruits dans une casserole avec 1 cuillère à soupe de sucre pendant 5 minutes.', 'timer' => 5],
                    ['text' => 'Servez le pain perdu chaud avec les fruits caramélisés par-dessus et un peu de cannelle.'],
                ],
                'ingredients' => [
                    ['name' => 'Pain rassis', 'quantity' => 6, 'unit' => 'tranches', 'category' => 'Boulangerie'],
                    ['name' => 'Œufs', 'quantity' => 3, 'unit' => 'pièce', 'category' => 'Produits frais'],
                    ['name' => 'Lait', 'quantity' => 200, 'unit' => 'ml', 'category' => 'Produits laitiers'],
                    ['name' => 'Sucre', 'quantity' => 3, 'unit' => 'c. à soupe', 'category' => 'Épicerie'],
                    ['name' => 'Beurre', 'quantity' => 30, 'unit' => 'g', 'category' => 'Produits laitiers'],
                    ['name' => 'Fruits abîmés (pommes, poires, bananes)', 'quantity' => 300, 'unit' => 'g', 'category' => 'Fruits'],
                    ['name' => 'Cannelle', 'quantity' => 1, 'unit' => 'c. à café', 'category' => 'Épices'],
                ],
            ],
            [
                'title' => 'Soupe anti-gaspi aux épluchures de légumes',
                'summary' => 'Une soupe zéro déchet délicieuse préparée avec les épluchures de vos légumes. Économique et pleine de saveurs !',
                'servings' => 6,
                'prep_minutes' => 15,
                'cook_minutes' => 30,
                'difficulty' => 'easy',
                'cuisine' => 'Française',
                'calories' => 120,
                'steps' => [
                    ['text' => 'Lavez soigneusement toutes les épluchures de légumes (carottes, pommes de terre, poireaux, courgettes).'],
                    ['text' => 'Dans une grande casserole, faites revenir 1 oignon émincé dans un filet d\'huile d\'olive pendant 3 minutes.', 'timer' => 3],
                    ['text' => 'Ajoutez toutes les épluchures de légumes et mélangez bien.'],
                    ['text' => 'Versez 1,5 litre de bouillon de légumes ou d\'eau avec un cube.'],
                    ['text' => 'Ajoutez 2 gousses d\'ail, du thym, du laurier, sel et poivre.'],
                    ['text' => 'Portez à ébullition puis laissez mijoter à feu doux pendant 25 minutes.', 'timer' => 25],
                    ['text' => 'Retirez le thym et le laurier, puis mixez la soupe jusqu\'à obtenir une texture lisse.'],
                    ['text' => 'Ajustez l\'assaisonnement et servez avec un filet de crème fraîche et des croûtons.'],
                ],
                'ingredients' => [
                    ['name' => 'Épluchures de légumes variés', 'quantity' => 500, 'unit' => 'g', 'category' => 'Légumes'],
                    ['name' => 'Oignon', 'quantity' => 1, 'unit' => 'pièce', 'category' => 'Légumes'],
                    ['name' => 'Huile d\'olive', 'quantity' => 2, 'unit' => 'c. à soupe', 'category' => 'Épicerie'],
                    ['name' => 'Bouillon de légumes', 'quantity' => 1.5, 'unit' => 'l', 'category' => 'Épicerie'],
                    ['name' => 'Ail', 'quantity' => 2, 'unit' => 'gousses', 'category' => 'Légumes'],
                    ['name' => 'Thym', 'quantity' => 2, 'unit' => 'branches', 'category' => 'Herbes'],
                    ['name' => 'Laurier', 'quantity' => 2, 'unit' => 'feuilles', 'category' => 'Herbes'],
                    ['name' => 'Crème fraîche', 'quantity' => 100, 'unit' => 'ml', 'category' => 'Produits laitiers'],
                ],
            ],
            [
                'title' => 'Gâteau au yaourt périmé et fruits flétris',
                'summary' => 'Le gâteau parfait pour utiliser vos yaourts en fin de date et vos fruits un peu flétris. Moelleux et délicieux !',
                'servings' => 8,
                'prep_minutes' => 15,
                'cook_minutes' => 35,
                'difficulty' => 'easy',
                'cuisine' => 'Française',
                'calories' => 280,
                'steps' => [
                    ['text' => 'Préchauffez votre four à 180°C (thermostat 6).'],
                    ['text' => 'Dans un saladier, versez 1 pot de yaourt et gardez le pot pour mesurer les autres ingrédients.'],
                    ['text' => 'Ajoutez 2 pots de sucre et mélangez bien.'],
                    ['text' => 'Incorporez 3 œufs un par un en mélangeant après chaque ajout.'],
                    ['text' => 'Ajoutez 3 pots de farine et 1 sachet de levure chimique. Mélangez jusqu\'à obtenir une pâte lisse.'],
                    ['text' => 'Versez 1/2 pot d\'huile et mélangez délicatement.'],
                    ['text' => 'Coupez vos fruits flétris en morceaux et incorporez-les à la pâte.'],
                    ['text' => 'Beurrez et farinez un moule à manqué, puis versez la pâte.'],
                    ['text' => 'Enfournez pour 35 minutes. Le gâteau est cuit quand la lame d\'un couteau ressort sèche.', 'timer' => 35],
                    ['text' => 'Laissez refroidir 10 minutes avant de démouler.', 'timer' => 10],
                ],
                'ingredients' => [
                    ['name' => 'Yaourt nature', 'quantity' => 1, 'unit' => 'pot', 'category' => 'Produits laitiers'],
                    ['name' => 'Sucre', 'quantity' => 2, 'unit' => 'pots', 'category' => 'Épicerie'],
                    ['name' => 'Œufs', 'quantity' => 3, 'unit' => 'pièce', 'category' => 'Produits frais'],
                    ['name' => 'Farine', 'quantity' => 3, 'unit' => 'pots', 'category' => 'Épicerie'],
                    ['name' => 'Levure chimique', 'quantity' => 1, 'unit' => 'sachet', 'category' => 'Épicerie'],
                    ['name' => 'Huile végétale', 'quantity' => 0.5, 'unit' => 'pot', 'category' => 'Épicerie'],
                    ['name' => 'Fruits flétris (pommes, poires, abricots)', 'quantity' => 200, 'unit' => 'g', 'category' => 'Fruits'],
                    ['name' => 'Beurre (pour le moule)', 'quantity' => 20, 'unit' => 'g', 'category' => 'Produits laitiers'],
                ],
            ],
            [
                'title' => 'Gratin de pâtes au fromage à finir',
                'summary' => 'Videz votre frigo avec ce gratin qui utilise vos restes de pâtes, vos bouts de fromage et vos légumes fatigués.',
                'servings' => 4,
                'prep_minutes' => 15,
                'cook_minutes' => 25,
                'difficulty' => 'easy',
                'cuisine' => 'Française',
                'calories' => 420,
                'steps' => [
                    ['text' => 'Préchauffez le four à 200°C (thermostat 6-7).'],
                    ['text' => 'Si vos pâtes ne sont pas déjà cuites, faites-les cuire dans de l\'eau bouillante salée selon les instructions du paquet.', 'timer' => 10],
                    ['text' => 'Pendant ce temps, coupez tous vos restes de légumes en petits morceaux.'],
                    ['text' => 'Dans une poêle, faites revenir les légumes avec un peu d\'huile pendant 5 minutes.', 'timer' => 5],
                    ['text' => 'Dans un grand saladier, mélangez les pâtes cuites égouttées avec les légumes.'],
                    ['text' => 'Ajoutez 200 ml de crème fraîche (ou lait), 2 œufs battus, sel, poivre et muscade.'],
                    ['text' => 'Râpez tous vos bouts de fromage et ajoutez-en la moitié dans le mélange.'],
                    ['text' => 'Versez le tout dans un plat à gratin beurré et parsemez du reste de fromage râpé.'],
                    ['text' => 'Enfournez pour 25 minutes jusqu\'à ce que le dessus soit bien doré et gratine.', 'timer' => 25],
                    ['text' => 'Laissez reposer 5 minutes avant de servir.', 'timer' => 5],
                ],
                'ingredients' => [
                    ['name' => 'Pâtes (restes ou 300g crues)', 'quantity' => 400, 'unit' => 'g', 'category' => 'Épicerie'],
                    ['name' => 'Légumes variés (tomates, courgettes, poivrons, champignons)', 'quantity' => 300, 'unit' => 'g', 'category' => 'Légumes'],
                    ['name' => 'Crème fraîche ou lait', 'quantity' => 200, 'unit' => 'ml', 'category' => 'Produits laitiers'],
                    ['name' => 'Œufs', 'quantity' => 2, 'unit' => 'pièce', 'category' => 'Produits frais'],
                    ['name' => 'Fromages variés à finir (emmental, comté, cheddar)', 'quantity' => 200, 'unit' => 'g', 'category' => 'Produits laitiers'],
                    ['name' => 'Huile d\'olive', 'quantity' => 2, 'unit' => 'c. à soupe', 'category' => 'Épicerie'],
                    ['name' => 'Muscade', 'quantity' => 1, 'unit' => 'pincée', 'category' => 'Épices'],
                ],
            ],
            [
                'title' => 'Chips de pelures de pommes de terre au four',
                'summary' => 'Transformez vos épluchures de pommes de terre en snack croustillant et sain. Zéro déchet et 100% gourmandise !',
                'servings' => 2,
                'prep_minutes' => 10,
                'cook_minutes' => 20,
                'difficulty' => 'easy',
                'cuisine' => 'Française',
                'calories' => 150,
                'steps' => [
                    ['text' => 'Préchauffez votre four à 200°C (thermostat 6-7).'],
                    ['text' => 'Lavez soigneusement les pelures de pommes de terre pour enlever toute trace de terre.'],
                    ['text' => 'Séchez-les bien avec un torchon propre.'],
                    ['text' => 'Dans un saladier, mélangez les pelures avec 2 cuillères à soupe d\'huile d\'olive.'],
                    ['text' => 'Ajoutez du sel, du poivre, et vos épices préférées (paprika, herbes de Provence, curry).'],
                    ['text' => 'Disposez les pelures en une seule couche sur une plaque recouverte de papier cuisson.'],
                    ['text' => 'Enfournez pour 15-20 minutes en remuant à mi-cuisson pour qu\'elles dorent uniformément.', 'timer' => 20],
                    ['text' => 'Surveillez la fin de cuisson : elles doivent être croustillantes et dorées mais pas brûlées.'],
                    ['text' => 'Laissez refroidir quelques minutes, elles vont devenir encore plus croustillantes.'],
                ],
                'ingredients' => [
                    ['name' => 'Pelures de pommes de terre', 'quantity' => 200, 'unit' => 'g', 'category' => 'Légumes'],
                    ['name' => 'Huile d\'olive', 'quantity' => 2, 'unit' => 'c. à soupe', 'category' => 'Épicerie'],
                    ['name' => 'Sel', 'quantity' => 1, 'unit' => 'c. à café', 'category' => 'Épicerie'],
                    ['name' => 'Paprika', 'quantity' => 1, 'unit' => 'c. à café', 'category' => 'Épices'],
                    ['name' => 'Herbes de Provence', 'quantity' => 1, 'unit' => 'c. à café', 'category' => 'Herbes'],
                ],
            ],
            [
                'title' => 'Cake salé aux légumes du frigo',
                'summary' => 'Utilisez tous vos légumes qui commencent à ramollir dans ce délicieux cake salé. Parfait pour l\'apéritif ou un pique-nique.',
                'servings' => 6,
                'prep_minutes' => 20,
                'cook_minutes' => 45,
                'difficulty' => 'medium',
                'cuisine' => 'Française',
                'calories' => 350,
                'steps' => [
                    ['text' => 'Préchauffez le four à 180°C (thermostat 6).'],
                    ['text' => 'Coupez tous vos légumes en petits dés (tomates, courgettes, poivrons, oignons, etc.).'],
                    ['text' => 'Faites revenir les légumes dans une poêle avec un peu d\'huile pendant 10 minutes jusqu\'à ce qu\'ils soient tendres.', 'timer' => 10],
                    ['text' => 'Dans un saladier, battez 3 œufs avec 200 ml de lait.'],
                    ['text' => 'Ajoutez 200g de farine, 1 sachet de levure, sel et poivre.'],
                    ['text' => 'Incorporez 100g de fromage râpé et les légumes refroidis.'],
                    ['text' => 'Ajoutez éventuellement des olives, des lardons ou du jambon coupés en morceaux.'],
                    ['text' => 'Versez la pâte dans un moule à cake beurré et fariné.'],
                    ['text' => 'Enfournez pour 45 minutes. Vérifiez la cuisson avec un couteau qui doit ressortir sec.', 'timer' => 45],
                    ['text' => 'Laissez tiédir avant de démouler et servir.'],
                ],
                'ingredients' => [
                    ['name' => 'Légumes variés', 'quantity' => 300, 'unit' => 'g', 'category' => 'Légumes'],
                    ['name' => 'Œufs', 'quantity' => 3, 'unit' => 'pièce', 'category' => 'Produits frais'],
                    ['name' => 'Lait', 'quantity' => 200, 'unit' => 'ml', 'category' => 'Produits laitiers'],
                    ['name' => 'Farine', 'quantity' => 200, 'unit' => 'g', 'category' => 'Épicerie'],
                    ['name' => 'Levure chimique', 'quantity' => 1, 'unit' => 'sachet', 'category' => 'Épicerie'],
                    ['name' => 'Fromage râpé', 'quantity' => 100, 'unit' => 'g', 'category' => 'Produits laitiers'],
                    ['name' => 'Huile d\'olive', 'quantity' => 3, 'unit' => 'c. à soupe', 'category' => 'Épicerie'],
                ],
            ],
            [
                'title' => 'Smoothie anti-gaspi aux fruits trop mûrs',
                'summary' => 'Un smoothie vitaminé pour sauver vos fruits trop mûrs. Frais, sain et zéro déchet !',
                'servings' => 2,
                'prep_minutes' => 5,
                'cook_minutes' => 0,
                'difficulty' => 'easy',
                'cuisine' => 'Française',
                'calories' => 180,
                'steps' => [
                    ['text' => 'Épluchez les fruits trop mûrs et coupez-les en morceaux en retirant les parties abîmées.'],
                    ['text' => 'Placez tous les fruits dans le blender.'],
                    ['text' => 'Ajoutez 1 banane pour la texture crémeuse (même si elle est très mûre, c\'est parfait !).'],
                    ['text' => 'Versez 300 ml de lait (végétal ou animal selon vos préférences).'],
                    ['text' => 'Ajoutez 1 cuillère à soupe de miel ou de sirop d\'agave si les fruits ne sont pas assez sucrés.'],
                    ['text' => 'Mixez le tout pendant 1 minute jusqu\'à obtenir une texture lisse et homogène.', 'timer' => 1],
                    ['text' => 'Ajoutez quelques glaçons et mixez encore 30 secondes pour un smoothie bien frais.'],
                    ['text' => 'Servez immédiatement dans de grands verres.'],
                ],
                'ingredients' => [
                    ['name' => 'Fruits trop mûrs variés', 'quantity' => 300, 'unit' => 'g', 'category' => 'Fruits'],
                    ['name' => 'Banane', 'quantity' => 1, 'unit' => 'pièce', 'category' => 'Fruits'],
                    ['name' => 'Lait ou boisson végétale', 'quantity' => 300, 'unit' => 'ml', 'category' => 'Produits laitiers'],
                    ['name' => 'Miel', 'quantity' => 1, 'unit' => 'c. à soupe', 'category' => 'Épicerie'],
                    ['name' => 'Glaçons', 'quantity' => 6, 'unit' => 'pièce', 'category' => 'Autre'],
                ],
            ],
            [
                'title' => 'Tarte aux fanes de radis et fromage de chèvre',
                'summary' => 'Ne jetez plus les fanes de radis ! Elles se transforment en une délicieuse tarte savoureuse et originale.',
                'servings' => 6,
                'prep_minutes' => 20,
                'cook_minutes' => 30,
                'difficulty' => 'medium',
                'cuisine' => 'Française',
                'calories' => 380,
                'steps' => [
                    ['text' => 'Préchauffez le four à 180°C (thermostat 6).'],
                    ['text' => 'Lavez soigneusement les fanes de radis et égouttez-les bien.'],
                    ['text' => 'Hachez grossièrement les fanes de radis.'],
                    ['text' => 'Dans une poêle, faites revenir 1 oignon émincé dans un peu d\'huile pendant 3 minutes.', 'timer' => 3],
                    ['text' => 'Ajoutez les fanes de radis hachées et faites-les revenir 5 minutes jusqu\'à ce qu\'elles soient fondues.', 'timer' => 5],
                    ['text' => 'Étalez la pâte feuilletée dans un moule à tarte et piquez le fond à la fourchette.'],
                    ['text' => 'Dans un bol, battez 3 œufs avec 200 ml de crème fraîche, sel, poivre et muscade.'],
                    ['text' => 'Répartissez les fanes de radis sur le fond de tarte, puis versez le mélange œufs-crème.'],
                    ['text' => 'Émiettez le fromage de chèvre sur le dessus.'],
                    ['text' => 'Enfournez pour 30 minutes jusqu\'à ce que la tarte soit dorée et bien prise.', 'timer' => 30],
                    ['text' => 'Laissez tiédir 10 minutes avant de servir.'],
                ],
                'ingredients' => [
                    ['name' => 'Fanes de radis', 'quantity' => 200, 'unit' => 'g', 'category' => 'Légumes'],
                    ['name' => 'Pâte feuilletée', 'quantity' => 1, 'unit' => 'rouleau', 'category' => 'Épicerie'],
                    ['name' => 'Oignon', 'quantity' => 1, 'unit' => 'pièce', 'category' => 'Légumes'],
                    ['name' => 'Œufs', 'quantity' => 3, 'unit' => 'pièce', 'category' => 'Produits frais'],
                    ['name' => 'Crème fraîche', 'quantity' => 200, 'unit' => 'ml', 'category' => 'Produits laitiers'],
                    ['name' => 'Fromage de chèvre', 'quantity' => 150, 'unit' => 'g', 'category' => 'Produits laitiers'],
                    ['name' => 'Huile d\'olive', 'quantity' => 2, 'unit' => 'c. à soupe', 'category' => 'Épicerie'],
                    ['name' => 'Muscade', 'quantity' => 1, 'unit' => 'pincée', 'category' => 'Épices'],
                ],
            ],
            [
                'title' => 'Compote de fruits abîmés maison',
                'summary' => 'La recette parfaite pour sauver tous vos fruits trop mûrs ou légèrement abîmés. Simple, rapide et délicieuse !',
                'servings' => 4,
                'prep_minutes' => 10,
                'cook_minutes' => 20,
                'difficulty' => 'easy',
                'cuisine' => 'Française',
                'calories' => 90,
                'steps' => [
                    ['text' => 'Épluchez tous vos fruits et retirez les parties vraiment trop abîmées ou pourries.'],
                    ['text' => 'Coupez les fruits en morceaux de taille moyenne.'],
                    ['text' => 'Placez les fruits dans une casserole avec 100 ml d\'eau.'],
                    ['text' => 'Ajoutez 2-3 cuillères à soupe de sucre selon la maturité des fruits (les fruits très mûrs en nécessitent moins).'],
                    ['text' => 'Ajoutez 1 gousse de vanille fendue ou 1 cuillère à café de cannelle selon vos goûts.'],
                    ['text' => 'Portez à ébullition puis baissez le feu et laissez mijoter à couvert pendant 20 minutes.', 'timer' => 20],
                    ['text' => 'Mélangez de temps en temps. Les fruits doivent être bien fondants.'],
                    ['text' => 'Pour une compote lisse, mixez-la une fois refroidie. Pour une texture avec morceaux, écrasez-la simplement à la fourchette.'],
                    ['text' => 'Conservez au réfrigérateur dans un bocal hermétique jusqu\'à 5 jours.'],
                ],
                'ingredients' => [
                    ['name' => 'Fruits abîmés variés (pommes, poires, pêches, abricots)', 'quantity' => 800, 'unit' => 'g', 'category' => 'Fruits'],
                    ['name' => 'Eau', 'quantity' => 100, 'unit' => 'ml', 'category' => 'Autre'],
                    ['name' => 'Sucre', 'quantity' => 3, 'unit' => 'c. à soupe', 'category' => 'Épicerie'],
                    ['name' => 'Vanille ou cannelle', 'quantity' => 1, 'unit' => 'gousse ou c. à café', 'category' => 'Épices'],
                ],
            ],
            [
                'title' => 'Bouillon de légumes aux chutes et épluchures',
                'summary' => 'Un bouillon maison zéro déchet préparé avec tous les restes de légumes. Parfait comme base pour soupes et risottos !',
                'servings' => 8,
                'prep_minutes' => 10,
                'cook_minutes' => 60,
                'difficulty' => 'easy',
                'cuisine' => 'Française',
                'calories' => 25,
                'steps' => [
                    ['text' => 'Rassemblez toutes vos chutes de légumes : épluchures de carottes, oignons, poireaux, bout de céleri, etc.'],
                    ['text' => 'Lavez soigneusement tous les légumes et épluchures.'],
                    ['text' => 'Placez tous les légumes dans une grande marmite.'],
                    ['text' => 'Ajoutez 2 litres d\'eau froide.'],
                    ['text' => 'Ajoutez 1 bouquet garni (thym, laurier, persil), 2 gousses d\'ail, du sel et quelques grains de poivre.'],
                    ['text' => 'Portez à ébullition puis réduisez le feu.'],
                    ['text' => 'Laissez mijoter à feu doux pendant 1 heure sans couvrir pour concentrer les saveurs.', 'timer' => 60],
                    ['text' => 'Filtrez le bouillon à travers une passoire fine en pressant bien les légumes pour extraire tout le jus.'],
                    ['text' => 'Laissez refroidir et conservez au réfrigérateur jusqu\'à 3 jours ou congelez en portions.'],
                ],
                'ingredients' => [
                    ['name' => 'Chutes et épluchures de légumes', 'quantity' => 500, 'unit' => 'g', 'category' => 'Légumes'],
                    ['name' => 'Eau', 'quantity' => 2, 'unit' => 'l', 'category' => 'Autre'],
                    ['name' => 'Thym', 'quantity' => 3, 'unit' => 'branches', 'category' => 'Herbes'],
                    ['name' => 'Laurier', 'quantity' => 2, 'unit' => 'feuilles', 'category' => 'Herbes'],
                    ['name' => 'Persil', 'quantity' => 5, 'unit' => 'branches', 'category' => 'Herbes'],
                    ['name' => 'Ail', 'quantity' => 2, 'unit' => 'gousses', 'category' => 'Légumes'],
                    ['name' => 'Poivre en grains', 'quantity' => 10, 'unit' => 'grains', 'category' => 'Épices'],
                ],
            ],
        ];
    }
}

