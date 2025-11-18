<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tenant;
use App\Models\User;
use App\Models\Project;
use App\Models\Stage;
use App\Models\Task;
use App\Models\Template;

class ProjectDataSeeder extends Seeder
{
    public function run(): void
    {
        // Get tenants and users
        $acmeTenant = Tenant::where('slug', 'acme-corp')->first();
        $startupTenant = Tenant::where('slug', 'startup-xyz')->first();
        
        $beatriz = User::where('email', 'beatriz@acme-corp.com')->first();
        $emilio = User::where('email', 'emilio@acme-corp.com')->first();
        $eder = User::where('email', 'eder@acme-corp.com')->first();

        if (!$acmeTenant || !$startupTenant) {
            $this->command->error('Tenants not found. Run TenantSeeder first.');
            return;
        }

        // Create Templates first
        $webDevTemplate = Template::create([
            'tenant_id' => $acmeTenant->id,
            'name' => 'Website Development Project',
            'description' => 'Standard website development workflow',
            'default_duration_days' => 60,
            'is_active' => true,
            'structure' => [
                'stages' => [
                    [
                        'name' => 'Planning & Analysis',
                        'description' => 'Project planning and requirements analysis',
                        'days_offset' => 0,
                        'duration_days' => 14,
                        'tasks' => [
                            ['name' => 'Requirements Gathering', 'description' => 'Collect and document requirements', 'estimated_hours' => 16],
                            ['name' => 'Technical Specification', 'description' => 'Create technical documentation', 'estimated_hours' => 12],
                            ['name' => 'Project Timeline', 'description' => 'Define project milestones', 'estimated_hours' => 8],
                        ]
                    ],
                    [
                        'name' => 'Design & Prototyping',
                        'description' => 'UI/UX design and prototyping',
                        'days_offset' => 14,
                        'duration_days' => 21,
                        'tasks' => [
                            ['name' => 'Wireframes', 'description' => 'Create wireframes and mockups', 'estimated_hours' => 20],
                            ['name' => 'UI Design', 'description' => 'Design user interface', 'estimated_hours' => 32],
                            ['name' => 'Prototype', 'description' => 'Build interactive prototype', 'estimated_hours' => 16],
                        ]
                    ],
                    [
                        'name' => 'Development',
                        'description' => 'Backend and frontend development',
                        'days_offset' => 35,
                        'duration_days' => 28,
                        'tasks' => [
                            ['name' => 'Backend Setup', 'description' => 'Setup backend infrastructure', 'estimated_hours' => 24],
                            ['name' => 'Frontend Development', 'description' => 'Implement frontend components', 'estimated_hours' => 40],
                            ['name' => 'API Integration', 'description' => 'Connect frontend with backend', 'estimated_hours' => 16],
                        ]
                    ]
                ]
            ]
        ]);

        // Create Projects using templates and manual projects
        
        // Project 1: From template
        $project1 = $webDevTemplate->createProject([
            'name' => 'Acme Corporate Website',
            'description' => 'New corporate website for Acme Corporation',
            'expected_start_at' => now(),
            'expected_end_at' => now()->addDays(60),
        ]);

        // Assign users to tasks in Project 1
        $project1Tasks = $project1->stages()->with('tasks')->get()->flatMap->tasks;
        if ($project1Tasks->count() > 0) {
            $project1Tasks[0]->update(['responsible_id' => $beatriz?->id, 'status' => 'completed', 'started_at' => now()->subDays(10), 'ended_at' => now()->subDays(8)]);
            $project1Tasks[1]->update(['responsible_id' => $emilio?->id, 'status' => 'in_progress', 'started_at' => now()->subDays(3)]);
            $project1Tasks[2]->update(['responsible_id' => $beatriz?->id]);
        }

        // Project 2: Manual project for StartupXYZ
        $project2 = Project::create([
            'tenant_id' => $startupTenant->id,
            'name' => 'Mobile App Development',
            'description' => 'FinTech mobile application for StartupXYZ',
            'status' => 'in_progress',
            'expected_start_at' => now()->subDays(30),
            'expected_end_at' => now()->addDays(90),
            'started_at' => now()->subDays(30),
            'created_by' => User::where('email', 'admin@startup-xyz.com')->first()?->id,
        ]);

        // Create stages for Project 2
        $stage1 = Stage::create([
            'tenant_id' => $startupTenant->id,
            'project_id' => $project2->id,
            'name' => 'MVP Development',
            'description' => 'Minimum Viable Product development',
            'status' => 'completed',
            'expected_start_at' => now()->subDays(30),
            'expected_end_at' => now()->subDays(10),
            'started_at' => now()->subDays(30),
            'ended_at' => now()->subDays(10),
            'responsible_id' => User::where('email', 'maria@startup-xyz.com')->first()?->id,
            'order' => 1,
        ]);

        $stage2 = Stage::create([
            'tenant_id' => $startupTenant->id,
            'project_id' => $project2->id,
            'name' => 'Testing & QA',
            'description' => 'Quality assurance and testing phase',
            'status' => 'in_progress',
            'expected_start_at' => now()->subDays(10),
            'expected_end_at' => now()->addDays(20),
            'started_at' => now()->subDays(10),
            'responsible_id' => User::where('email', 'maria@startup-xyz.com')->first()?->id,
            'order' => 2,
        ]);

        $stage3 = Stage::create([
            'tenant_id' => $startupTenant->id,
            'project_id' => $project2->id,
            'name' => 'Deployment',
            'description' => 'Production deployment and monitoring',
            'status' => 'planned',
            'expected_start_at' => now()->addDays(20),
            'expected_end_at' => now()->addDays(35),
            'dependent_stage_id' => $stage2->id,
            'responsible_id' => User::where('email', 'admin@startup-xyz.com')->first()?->id,
            'order' => 3,
        ]);

        // Create tasks for Project 2
        $tasks = [
            // Stage 1 tasks
            [
                'tenant_id' => $startupTenant->id,
                'stage_id' => $stage1->id,
                'name' => 'User Authentication',
                'description' => 'Implement user login and registration',
                'status' => 'completed',
                'expected_start_at' => now()->subDays(30),
                'expected_end_at' => now()->subDays(25),
                'started_at' => now()->subDays(30),
                'ended_at' => now()->subDays(25),
                'responsible_id' => User::where('email', 'maria@startup-xyz.com')->first()?->id,
                'order' => 1,
                'estimated_hours' => 40,
                'actual_hours' => 38,
            ],
            [
                'tenant_id' => $startupTenant->id,
                'stage_id' => $stage1->id,
                'name' => 'Dashboard Implementation',
                'description' => 'Create main dashboard interface',
                'status' => 'completed',
                'expected_start_at' => now()->subDays(25),
                'expected_end_at' => now()->subDays(15),
                'started_at' => now()->subDays(25),
                'ended_at' => now()->subDays(15),
                'responsible_id' => User::where('email', 'maria@startup-xyz.com')->first()?->id,
                'order' => 2,
                'estimated_hours' => 32,
                'actual_hours' => 35,
            ],
            
            // Stage 2 tasks
            [
                'tenant_id' => $startupTenant->id,
                'stage_id' => $stage2->id,
                'name' => 'Unit Testing',
                'description' => 'Write and execute unit tests',
                'status' => 'in_progress',
                'expected_start_at' => now()->subDays(10),
                'expected_end_at' => now()->addDays(5),
                'started_at' => now()->subDays(5),
                'responsible_id' => User::where('email', 'maria@startup-xyz.com')->first()?->id,
                'order' => 1,
                'estimated_hours' => 24,
            ],
            [
                'tenant_id' => $startupTenant->id,
                'stage_id' => $stage2->id,
                'name' => 'User Acceptance Testing',
                'description' => 'Client testing and feedback collection',
                'status' => 'planned',
                'expected_start_at' => now()->addDays(5),
                'expected_end_at' => now()->addDays(15),
                'responsible_id' => User::where('email', 'client@startup-xyz.com')->first()?->id,
                'order' => 2,
                'estimated_hours' => 16,
            ],
            
            // Stage 3 tasks
            [
                'tenant_id' => $startupTenant->id,
                'stage_id' => $stage3->id,
                'name' => 'Production Setup',
                'description' => 'Configure production environment',
                'status' => 'planned',
                'expected_start_at' => now()->addDays(20),
                'expected_end_at' => now()->addDays(25),
                'responsible_id' => User::where('email', 'admin@startup-xyz.com')->first()?->id,
                'order' => 1,
                'estimated_hours' => 20,
            ],
            [
                'tenant_id' => $startupTenant->id,
                'stage_id' => $stage3->id,
                'name' => 'Monitoring Setup',
                'description' => 'Setup application monitoring',
                'status' => 'planned',
                'expected_start_at' => now()->addDays(25),
                'expected_end_at' => now()->addDays(30),
                'responsible_id' => User::where('email', 'admin@startup-xyz.com')->first()?->id,
                'order' => 2,
                'estimated_hours' => 12,
            ],
        ];

        foreach ($tasks as $taskData) {
            Task::create($taskData);
        }

        // Project 3: Simple project for Acme
        $project3 = Project::create([
            'tenant_id' => $acmeTenant->id,
            'name' => 'Database Migration Project',
            'description' => 'Migrate legacy database to new system',
            'status' => 'planned',
            'expected_start_at' => now()->addDays(7),
            'expected_end_at' => now()->addDays(45),
            'created_by' => $beatriz?->id,
        ]);

        $stage4 = Stage::create([
            'tenant_id' => $acmeTenant->id,
            'project_id' => $project3->id,
            'name' => 'Data Analysis',
            'description' => 'Analyze current database structure',
            'status' => 'planned',
            'expected_start_at' => now()->addDays(7),
            'expected_end_at' => now()->addDays(21),
            'responsible_id' => $emilio?->id,
            'order' => 1,
        ]);

        Task::create([
            'tenant_id' => $acmeTenant->id,
            'stage_id' => $stage4->id,
            'name' => 'Database Schema Review',
            'description' => 'Review and document current database schema',
            'status' => 'planned',
            'expected_start_at' => now()->addDays(7),
            'expected_end_at' => now()->addDays(14),
            'responsible_id' => $emilio?->id,
            'order' => 1,
            'estimated_hours' => 30,
        ]);

        $this->command->info('Sample projects, stages, and tasks created successfully!');
        $this->command->info('Created:');
        $this->command->info('- 1 Template (Website Development)');
        $this->command->info('- 3 Projects (Acme Corporate Website, Mobile App, Database Migration)');
        $this->command->info('- 7 Stages with various statuses');
        $this->command->info('- 10+ Tasks with different assignments and progress');
    }
}