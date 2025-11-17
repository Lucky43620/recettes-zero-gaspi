<?php

namespace Database\Seeders;

use App\Models\Ingredient;
use App\Models\Unit;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class IngredientSeeder extends Seeder
{
    public function run(): void
    {
        // Clear existing data
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Ingredient::truncate();
        Unit::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Create units first
        $units = [
            ['code' => 'g', 'label' => 'gramme'],
            ['code' => 'kg', 'label' => 'kilogramme'],
            ['code' => 'mg', 'label' => 'milligramme'],
            ['code' => 'ml', 'label' => 'millilitre'],
            ['code' => 'l', 'label' => 'litre'],
            ['code' => 'cl', 'label' => 'centilitre'],
            ['code' => 'dl', 'label' => 'décilitre'],
            ['code' => 'piece', 'label' => 'pièce'],
            ['code' => 'pinch', 'label' => 'pincée'],
            ['code' => 'tbsp', 'label' => 'cuillère à soupe'],
            ['code' => 'tsp', 'label' => 'cuillère à café'],
            ['code' => 'cup', 'label' => 'tasse'],
            ['code' => 'bunch', 'label' => 'botte'],
            ['code' => 'slice', 'label' => 'tranche'],
            ['code' => 'can', 'label' => 'boîte'],
            ['code' => 'pack', 'label' => 'paquet'],
            ['code' => 'jar', 'label' => 'pot'],
            ['code' => 'branch', 'label' => 'branche'],
            ['code' => 'leaf', 'label' => 'feuille'],
            ['code' => 'clove', 'label' => 'gousse'],
            ['code' => 'sachet', 'label' => 'sachet'],
            ['code' => 'roll', 'label' => 'rouleau'],
            ['code' => 'grain', 'label' => 'grain'],
        ];

        foreach ($units as $unit) {
            Unit::create($unit);
        }

        // Create common French ingredients by category
        $ingredients = [
            // Légumes
            'Tomate', 'Oignon', 'Ail', 'Carotte', 'Pomme de terre', 'Poivron rouge', 'Poivron vert',
            'Courgette', 'Aubergine', 'Concombre', 'Laitue', 'Épinard', 'Brocoli', 'Chou-fleur',
            'Haricot vert', 'Petit pois', 'Navet', 'Poireau', 'Céleri', 'Fenouil', 'Betterave',
            'Radis', 'Champignon', 'Potiron', 'Courge', 'Patate douce', 'Endive', 'Roquette',
            'Mâche', 'Cresson', 'Chou rouge', 'Chou blanc', 'Chou de Bruxelles', 'Artichaut',
            'Asperge', 'Maïs', 'Avocat', 'Gingembre',

            // Fruits
            'Pomme', 'Poire', 'Banane', 'Orange', 'Citron', 'Citron vert', 'Pamplemousse',
            'Fraise', 'Framboise', 'Myrtille', 'Mûre', 'Cerise', 'Abricot', 'Pêche', 'Nectarine',
            'Prune', 'Raisin', 'Melon', 'Pastèque', 'Kiwi', 'Ananas', 'Mangue', 'Papaye',
            'Fruit de la passion', 'Figue', 'Datte', 'Raisin sec',

            // Viandes
            'Poulet', 'Bœuf', 'Porc', 'Agneau', 'Veau', 'Dinde', 'Canard', 'Lapin',
            'Jambon', 'Lard', 'Saucisse', 'Merguez', 'Chorizo', 'Bacon',

            // Poissons et fruits de mer
            'Saumon', 'Thon', 'Cabillaud', 'Sole', 'Dorade', 'Bar', 'Truite', 'Maquereau',
            'Sardine', 'Anchois', 'Crevette', 'Moule', 'Huître', 'Saint-Jacques', 'Calmar',
            'Crabe', 'Homard',

            // Produits laitiers
            'Lait', 'Crème fraîche', 'Beurre', 'Yaourt nature', 'Fromage blanc', 'Crème liquide',
            'Gruyère', 'Emmental', 'Parmesan', 'Mozzarella', 'Chèvre', 'Roquefort', 'Camembert',
            'Brie', 'Comté', 'Feta', 'Ricotta', 'Mascarpone',

            // Féculents et céréales
            'Riz', 'Pâtes', 'Spaghetti', 'Tagliatelle', 'Penne', 'Fusilli', 'Couscous',
            'Quinoa', 'Boulgour', 'Semoule', 'Polenta', 'Farine de blé', 'Farine complète',
            'Pain', 'Pain de mie', 'Baguette', 'Tortilla', 'Nouilles chinoises',

            // Légumineuses et conserves
            'Lentilles', 'Pois chiches', 'Haricots rouges', 'Haricots blancs', 'Fèves',
            'Tomate en conserve', 'Tomate concentrée', 'Sauce tomate', 'Coulis de tomate',
            'Thon en boîte', 'Maïs en conserve', 'Pois en conserve',

            // Huiles et condiments
            'Huile d\'olive', 'Huile de tournesol', 'Huile de colza', 'Vinaigre balsamique',
            'Vinaigre de vin', 'Vinaigre de cidre', 'Moutarde', 'Mayonnaise', 'Ketchup',
            'Sauce soja', 'Sauce worcestershire', 'Tabasco', 'Harissa',

            // Épices et aromates
            'Sel', 'Poivre', 'Paprika', 'Cumin', 'Curry', 'Curcuma', 'Cannelle', 'Muscade',
            'Gingembre en poudre', 'Coriandre', 'Basilic', 'Persil', 'Thym', 'Romarin',
            'Laurier', 'Origan', 'Menthe', 'Ciboulette', 'Estragon', 'Safran', 'Vanille',

            // Sucres et produits sucrés
            'Sucre', 'Sucre vanillé', 'Sucre glace', 'Cassonade', 'Miel', 'Sirop d\'érable',
            'Confiture', 'Nutella', 'Chocolat noir', 'Chocolat au lait', 'Chocolat blanc',
            'Cacao en poudre', 'Pépites de chocolat',

            // Autres
            'Œuf', 'Levure chimique', 'Bicarbonate', 'Gélatine', 'Agar-agar', 'Fécule de maïs',
            'Eau', 'Vin blanc', 'Vin rouge', 'Bouillon de volaille', 'Bouillon de bœuf',
            'Bouillon de légumes', 'Noix', 'Amande', 'Noisette', 'Pistache', 'Cacahuète',
        ];

        foreach ($ingredients as $ingredient) {
            Ingredient::create([
                'name' => $ingredient,
                'category' => $this->getCategory($ingredient),
                'created_at' => now()->subDays(rand(1, 365)),
            ]);
        }
    }

    private function getCategory(string $ingredient): string
    {
        $categories = [
            'légumes' => ['Tomate', 'Oignon', 'Ail', 'Carotte', 'Pomme de terre', 'Poivron', 'Courgette', 'Aubergine', 'Concombre', 'Laitue', 'Épinard', 'Brocoli', 'Chou', 'Haricot', 'Pois', 'Navet', 'Poireau', 'Céleri', 'Fenouil', 'Betterave', 'Radis', 'Champignon', 'Potiron', 'Courge', 'Patate', 'Endive', 'Roquette', 'Mâche', 'Cresson', 'Artichaut', 'Asperge', 'Maïs', 'Avocat'],
            'fruits' => ['Pomme', 'Poire', 'Banane', 'Orange', 'Citron', 'Pamplemousse', 'Fraise', 'Framboise', 'Myrtille', 'Mûre', 'Cerise', 'Abricot', 'Pêche', 'Nectarine', 'Prune', 'Raisin', 'Melon', 'Pastèque', 'Kiwi', 'Ananas', 'Mangue', 'Papaye', 'Passion', 'Figue', 'Datte'],
            'viandes' => ['Poulet', 'Bœuf', 'Porc', 'Agneau', 'Veau', 'Dinde', 'Canard', 'Lapin', 'Jambon', 'Lard', 'Saucisse', 'Merguez', 'Chorizo', 'Bacon'],
            'poissons' => ['Saumon', 'Thon', 'Cabillaud', 'Sole', 'Dorade', 'Bar', 'Truite', 'Maquereau', 'Sardine', 'Anchois', 'Crevette', 'Moule', 'Huître', 'Saint-Jacques', 'Calmar', 'Crabe', 'Homard'],
            'produits laitiers' => ['Lait', 'Crème', 'Beurre', 'Yaourt', 'Fromage', 'Gruyère', 'Emmental', 'Parmesan', 'Mozzarella', 'Chèvre', 'Roquefort', 'Camembert', 'Brie', 'Comté', 'Feta', 'Ricotta', 'Mascarpone'],
            'féculents' => ['Riz', 'Pâtes', 'Spaghetti', 'Tagliatelle', 'Penne', 'Fusilli', 'Couscous', 'Quinoa', 'Boulgour', 'Semoule', 'Polenta', 'Farine', 'Pain', 'Baguette', 'Tortilla', 'Nouilles'],
            'légumineuses' => ['Lentilles', 'Pois chiches', 'Haricots', 'Fèves'],
            'épices' => ['Sel', 'Poivre', 'Paprika', 'Cumin', 'Curry', 'Curcuma', 'Cannelle', 'Muscade', 'Gingembre', 'Coriandre', 'Basilic', 'Persil', 'Thym', 'Romarin', 'Laurier', 'Origan', 'Menthe', 'Ciboulette', 'Estragon', 'Safran', 'Vanille'],
        ];

        foreach ($categories as $category => $keywords) {
            foreach ($keywords as $keyword) {
                if (str_contains($ingredient, $keyword)) {
                    return $category;
                }
            }
        }

        return 'autres';
    }
}
