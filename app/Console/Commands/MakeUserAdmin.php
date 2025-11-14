<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

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

        if ($user->hasRole('admin')) {
            $this->warn("User '{$user->name}' is already an admin.");
            return Command::SUCCESS;
        }

        $user->assignRole('admin');

        $this->info("User '{$user->name}' ({$email}) has been promoted to admin.");

        return Command::SUCCESS;
    }
}
