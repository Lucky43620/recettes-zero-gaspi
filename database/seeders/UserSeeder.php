<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Clear existing data
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        User::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Create admin user
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@recettes.fr',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'bio' => 'Administrateur du site Recettes Zero Gaspi',
        ]);

        // Create test user
        User::create([
            'name' => 'Jean Dupont',
            'email' => 'jean.dupont@example.fr',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'bio' => 'Passionné de cuisine française et de recettes anti-gaspillage',
        ]);

        // Create diverse users with French names
        $names = [
            ['Marie', 'Martin', 'Chef passionnée spécialisée dans la cuisine méditerranéenne'],
            ['Pierre', 'Bernard', 'Amateur de pâtisserie et de desserts créatifs'],
            ['Sophie', 'Dubois', 'Maman de trois enfants qui adore cuisiner des plats simples'],
            ['Luc', 'Thomas', 'Étudiant en école de cuisine, toujours à la recherche de nouvelles idées'],
            ['Camille', 'Robert', 'Végétarienne convaincue qui partage ses meilleures recettes végé'],
            ['Antoine', 'Richard', 'Passionné de cuisine asiatique et de wok'],
            ['Julie', 'Petit', 'Adepte du batch cooking et meal prep pour toute la semaine'],
            ['Nicolas', 'Durand', 'Grand amateur de barbecue et cuisine au feu de bois'],
            ['Isabelle', 'Leroy', 'Experte en cuisine de saison et produits locaux'],
            ['François', 'Moreau', 'Cuisinier professionnel à la retraite qui partage son expérience'],
            ['Céline', 'Simon', 'Blogueuse culinaire spécialisée dans les recettes rapides'],
            ['Olivier', 'Laurent', 'Papa solo qui cuisine pour ses deux enfants'],
            ['Émilie', 'Lefebvre', 'Nutritionniste qui propose des recettes équilibrées'],
            ['Alexandre', 'Michel', 'Passionné de cuisine italienne authentique'],
            ['Nathalie', 'Garcia', 'Spécialiste des recettes sans gluten et sans lactose'],
            ['Julien', 'Martinez', 'Amateur de street food et burgers maison'],
            ['Aurélie', 'Rodriguez', 'Adepte du fait-maison et zéro déchet en cuisine'],
            ['Thomas', 'Sanchez', 'Chef pâtissier qui adore partager ses créations'],
            ['Valérie', 'Lopez', 'Grand-mère qui transmet ses recettes traditionnelles'],
            ['David', 'Gonzalez', 'Cuisinier autodidacte passionné de fusion food'],
            ['Caroline', 'Perez', 'Étudiante qui cuisine avec un petit budget'],
            ['Marc', 'Blanc', 'Chasseur et amateur de gibier et plats du terroir'],
            ['Sandrine', 'Guerin', 'Maman qui cuisine des goûters sains pour ses enfants'],
            ['Philippe', 'Boyer', 'Retraité qui redécouvre le plaisir de cuisiner'],
            ['Laure', 'Muller', 'Végane qui prouve que la cuisine végétale peut être délicieuse'],
        ];

        foreach ($names as $index => $name) {
            User::create([
                'name' => $name[0] . ' ' . $name[1],
                'email' => strtolower($name[0] . '.' . $name[1]) . '@example.fr',
                'password' => Hash::make('password'),
                'email_verified_at' => now()->subDays(rand(1, 365)),
                'bio' => $name[2],
                'created_at' => now()->subDays(rand(1, 730)),
            ]);
        }
    }
}
