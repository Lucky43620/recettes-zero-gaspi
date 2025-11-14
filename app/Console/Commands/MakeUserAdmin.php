<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;

class MakeUserAdmin extends Command
{
    protected $signature = 'user:make-admin {email}';

    protected $description = 'Promote a user to admin role';

    public function handle(): int
    {
        $email = $this->argument('email');

        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->error("User with email '{$email}' not found.");
            return Command::FAILURE;
        }

        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);

        if ($user->hasRole('admin')) {
            $this->warn("User '{$user->name}' is already an admin.");
            return Command::SUCCESS;
        }

        $user->assignRole('admin');

        \Artisan::call('cache:clear');
        \Artisan::call('config:clear');
        \Artisan::call('permission:cache-reset');

        $this->info("User '{$user->name}' ({$email}) has been promoted to admin.");
        $this->warn("All caches cleared (including permissions). User must logout and login again.");

        return Command::SUCCESS;
    }
}
