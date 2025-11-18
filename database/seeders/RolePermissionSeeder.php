<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Create permissions
        $permissions = [
            // Project permissions
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

            // Stage permissions
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

            // Task permissions
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

            // Template permissions
            'template.view',
            'template.create',
            'template.edit',
            'template.delete',
            'template.*',

            // User permissions
            'user.view',
            'user.create',
            'user.edit',
            'user.delete',
            'user.*',

            // Tenant permissions
            'tenant.view',
            'tenant.create',
            'tenant.edit',
            'tenant.delete',
            'tenant.*',

            // Report permissions
            'report.view',
            'report.create',
            'report.export',
            'report.*',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles
        $superAdminRole = Role::firstOrCreate(['name' => 'super_admin']);
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $operatorRole = Role::firstOrCreate(['name' => 'operator']);
        $clientRole = Role::firstOrCreate(['name' => 'client']);

        // Assign permissions to roles
        
        // Super Admin gets all permissions
        $superAdminRole->givePermissionTo(Permission::all());

        // Admin gets all permissions except tenant management
        $adminRole->givePermissionTo([
            'project.*',
            'stage.*',
            'task.*',
            'template.*',
            'user.view',
            'user.create',
            'user.edit',
            'user.delete',
            'report.*',
        ]);

        // Operator gets limited permissions
        $operatorRole->givePermissionTo([
            'project.view',
            'stage.view',
            'stage.edit',
            'stage.start',
            'stage.pause',
            'stage.resume',
            'stage.complete',
            'task.view',
            'task.edit',
            'task.start',
            'task.pause',
            'task.resume',
            'task.complete',
            'template.view',
            'report.view',
        ]);

        // Client gets very limited permissions
        $clientRole->givePermissionTo([
            'project.view',
            'stage.view',
            'task.view',
            'task.edit',
            'task.start',
            'task.pause',
            'task.resume',
            'task.complete',
            'report.view',
        ]);

        $this->command->info('Roles and permissions created successfully!');
    }
}