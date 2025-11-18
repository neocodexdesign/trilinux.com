<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class TaskReports extends Page
{
    protected string $view = 'filament.pages.task-reports';
    protected static ?string $title = 'Relatórios de Tarefas';
    protected static ?string $navigationLabel = 'Relatórios';
    protected static ?int $navigationSort = 50;

    public static function canAccess(): bool
    {
        $user = Auth::user();
        return in_array($user->role, ['superuser', 'admin', 'manager']);
    }

    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-chart-bar';
    }
}
