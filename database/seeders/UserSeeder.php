<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Tenant;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Get tenants
        $neocodexTenant = Tenant::where('slug', 'neocodex-labs')->first();
        $acmeTenant = Tenant::where('slug', 'acme-corp')->first();
        $startupTenant = Tenant::where('slug', 'startup-xyz')->first();

        // Get roles
        $superAdminRole = Role::where('name', 'super_admin')->first();
        $adminRole = Role::where('name', 'admin')->first();
        $operatorRole = Role::where('name', 'operator')->first();
        $clientRole = Role::where('name', 'client')->first();

        $users = [
            // Superuser - Neocodex Labs
            [
                'name' => 'Neocodex Admin',
                'email' => 'admin@neocodex.com',
                'password' => Hash::make('password'),
                'tenant_id' => null, // Superuser doesn't belong to specific tenant
                'role' => 'superuser',
                'spatie_role' => $superAdminRole,
            ],
            
            // Custom Superuser - Emilio
            [
                'name' => 'Emilio Dami Silva',
                'email' => 'emiliodami@gmail.com',
                'username' => 'emiliodami',
                'password' => Hash::make('12345678'),
                'tenant_id' => null, // Superuser doesn't belong to specific tenant
                'role' => 'superuser',
                'spatie_role' => $superAdminRole,
            ],
            
            // Admin users for each tenant
            [
                'name' => 'Beatriz Silva',
                'email' => 'beatriz@acme-corp.com',
                'password' => Hash::make('password'),
                'tenant_id' => $acmeTenant?->id,
                'role' => 'admin',
                'spatie_role' => $adminRole,
            ],
            [
                'name' => 'Admin StartupXYZ',
                'email' => 'admin@startup-xyz.com',
                'password' => Hash::make('password'),
                'tenant_id' => $startupTenant?->id,
                'role' => 'admin',
                'spatie_role' => $adminRole,
            ],

            // Operators
            [
                'name' => 'Emilio da Silva',
                'email' => 'emilio@acme-corp.com',
                'password' => Hash::make('password'),
                'tenant_id' => $acmeTenant?->id,
                'role' => 'operator',
                'spatie_role' => $operatorRole,
            ],
            [
                'name' => 'Maria Santos',
                'email' => 'maria@startup-xyz.com',
                'password' => Hash::make('password'),
                'tenant_id' => $startupTenant?->id,
                'role' => 'operator',
                'spatie_role' => $operatorRole,
            ],

            // Clients
            [
                'name' => 'Eder Oliveira',
                'email' => 'eder@acme-corp.com',
                'password' => Hash::make('password'),
                'tenant_id' => $acmeTenant?->id,
                'role' => 'client',
                'spatie_role' => $clientRole,
            ],
            [
                'name' => 'Client StartupXYZ',
                'email' => 'client@startup-xyz.com',
                'password' => Hash::make('password'),
                'tenant_id' => $startupTenant?->id,
                'role' => 'client',
                'spatie_role' => $clientRole,
            ],
        ];

        foreach ($users as $userData) {
            $spatieRole = $userData['spatie_role'];
            unset($userData['spatie_role']);

            $user = User::firstOrCreate(
                ['email' => $userData['email']],
                $userData
            );

            // Assign role
            if ($spatieRole && !$user->hasRole($spatieRole->name)) {
                $user->assignRole($spatieRole);
            }
        }

        $this->command->info('Users created successfully!');
        $this->command->info('Default password for all users: password');
        $this->command->info('Superusers: emiliodami@gmail.com, admin@neocodex.com');
        $this->command->info('Tenant Admins have full access within their tenant only.');
    }
}