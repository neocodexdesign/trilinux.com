<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class TestSuperuser extends Command
{
    protected $signature = 'test:superuser {email}';
    protected $description = 'Test superuser methods';

    public function handle()
    {
        $email = $this->argument('email');

        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->error("User not found!");
            return 1;
        }

        $this->info("User: {$user->name}");
        $this->info("Email: {$user->email}");
        $this->info("Role (column): {$user->role}");
        $this->newLine();

        // Test isSuperuser()
        try {
            $result = $user->isSuperuser();
            $this->info("isSuperuser(): " . ($result ? 'TRUE' : 'FALSE'));
        } catch (\Exception $e) {
            $this->error("isSuperuser() ERROR: " . $e->getMessage());
        }

        // Test canAccessFilament()
        try {
            $result = $user->canAccessFilament();
            $this->info("canAccessFilament(): " . ($result ? 'TRUE' : 'FALSE'));
        } catch (\Exception $e) {
            $this->error("canAccessFilament() ERROR: " . $e->getMessage());
        }

        // Test Spatie roles
        try {
            if (method_exists($user, 'getRoleNames')) {
                $roles = $user->getRoleNames();
                $this->info("Spatie Roles: " . ($roles->isEmpty() ? 'NONE' : $roles->implode(', ')));
            }
        } catch (\Exception $e) {
            $this->error("getRoleNames() ERROR: " . $e->getMessage());
        }

        return 0;
    }
}