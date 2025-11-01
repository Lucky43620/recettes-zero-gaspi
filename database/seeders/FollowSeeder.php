<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FollowSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('followers')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $users = User::all();

        foreach ($users as $user) {
            $followCount = rand(3, 12);
            $toFollow = $users->where('id', '!=', $user->id)->random($followCount);
            
            foreach ($toFollow as $followedUser) {
                DB::table('followers')->insert([
                    'follower_id' => $user->id,
                    'following_id' => $followedUser->id,
                ]);
            }
        }
    }
}
