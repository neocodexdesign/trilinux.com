<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Tenant;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class SetupTenantPermissions extends Command
{
    protected $signature = 'tenant:permissions {tenant_id} {email}';
    protected $description = 'Setup all permissions for a tenant admin user';

    public function handle()
    {
        $tenantId = $this->argument('tenant_id');
        $email = $this->argument('email');

        $tenant = Tenant::find($tenantId);

        if (!$tenant) {
            $this->error("Tenant '{$tenantId}' not found!");
            return 1;
        }

        tenancy()->initialize($tenant);
        $this->info("Tenant initialized: {$tenant->id}");

        $admin = User::where('email', $email)->first();

        if (!$admin) {
            $this->error("User '{$email}' not found!");
            return 1;
        }

        $this->info("User found: {$admin->name} ({$admin->email})");

        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);

        if (!$admin->hasRole('admin')) {
            $admin->assignRole('admin');
        }

        // Todas as permissões com PONTO
        $allPermissions = [
            // Project
            'project.view',
            'project.create',
            'project.edit',
            'project.delete',
            'project.start',
            'project.pause',
            'project.resume',
            'project.complete',
            'project.review',
            'project.*',

            // Stage
            'stage.view',
            'stage.create',
            'stage.edit',
            'stage.delete',
            'stage.start',
            'stage.pause',
            'stage.resume',
            'stage.complete',
            'stage.review',
            'stage.*',

            // Task
            'task.view',
            'task.create',
            'task.edit',
            'task.delete',
            'task.start',
            'task.pause',
            'task.resume',
            'task.complete',
            'task.review',
            'task.reopen',
            'task.*',

            // User
            'user.view',
            'user.create',
            'user.edit',
            'user.delete',
            'user.assign_role',
            'user.assign_permissions',
            'user.*',

            // Tenant
            'tenant.view',
            'tenant.create',
            'tenant.edit',
            'tenant.delete',
            'tenant.*',

            // General
            'page.Dashboard',
        ];

        $this->info("\nCreating permissions...");
        $bar = $this->output->createProgressBar(count($allPermissions));

        foreach ($allPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();

        $adminRole->syncPermissions($allPermissions);

        $admin->refresh();

        $this->newLine();
        $this->info("Setup completed!");
        $this->table(
            ['Property', 'Value'],
            [
                ['User', $admin->email],
                ['Role', 'admin'],
                ['Permissions', $admin->getAllPermissions()->count()],
            ]
        );

        return 0;
    }
}