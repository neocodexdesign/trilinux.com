<?php

use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use App\Livewire\Settings\TwoFactor;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

Route::get('/', function () {
    return view('landing');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    // My Tasks Routes
    Route::view('tasks/my/pending', 'tasks.my.pending')->name('tasks.my.pending');
    Route::view('tasks/my/active', 'tasks.my.active')->name('tasks.my.active');
    Route::view('tasks/my/paused', 'tasks.my.paused')->name('tasks.my.paused');
    Route::view('tasks/my/completed', 'tasks.my.completed')->name('tasks.my.completed');

    // Team Tasks Routes
    Route::view('tasks/team/pending', 'tasks.team.pending')->name('tasks.team.pending');
    Route::view('tasks/team/active', 'tasks.team.active')->name('tasks.team.active');
    Route::view('tasks/team/paused', 'tasks.team.paused')->name('tasks.team.paused');
    Route::view('tasks/team/completed', 'tasks.team.completed')->name('tasks.team.completed');

    // Task Management (with Notes & Attachments)
    Route::get('tasks/{task}/manage', function ($taskId) {
        $task = \App\Models\Task::with(['stage.project', 'notes', 'attachments'])->findOrFail($taskId);
        return view('tasks.manage', ['task' => $task]);
    })->name('tasks.manage');

    // Stage Management (with Notes & Attachments)
    Route::get('stages/{stage}/manage', function ($stageId) {
        $stage = \App\Models\Stage::with(['project', 'notes', 'attachments'])->findOrFail($stageId);
        return view('stages.manage', ['stage' => $stage]);
    })->name('stages.manage');

    // Project Management (with Notes & Attachments)
    Route::get('projects/{project}/manage', function ($projectId) {
        $project = \App\Models\Project::with(['notes', 'attachments'])->findOrFail($projectId);
        return view('projects.manage', ['project' => $project]);
    })->name('projects.manage');

    // Attachments
    Route::get('attachments/{attachment}/download', function ($attachmentId) {
        $attachment = \App\Models\Attachment::findOrFail($attachmentId);
        $path = storage_path('app/public/attachments/' . $attachment->stored_filename);

        if (!file_exists($path)) {
            abort(404, 'Arquivo não encontrado');
        }

        return response()->download($path, $attachment->filename);
    })->name('attachments.download');

    // Settings Routes
    Route::redirect('settings', 'settings/profile');

    Route::get('settings/profile', Profile::class)->name('settings.profile');
    Route::get('settings/password', Password::class)->name('settings.password');
    Route::get('settings/appearance', Appearance::class)->name('settings.appearance');

    Route::get('settings/two-factor', TwoFactor::class)
        ->middleware(
            when(
                Features::canManageTwoFactorAuthentication()
                    && Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword'),
                ['password.confirm'],
                [],
            ),
        )
        ->name('two-factor.show');
});

require __DIR__.'/auth.php';
