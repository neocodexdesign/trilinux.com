<?php

namespace App\Filament\Tenant\Resources\Projects\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Hidden;

class ProjectForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Hidden::make('tenant_id')
                    ->default(auth()->user()?->tenant_id),
                
                TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->label('Nome do Projeto'),
                
                Textarea::make('description')
                    ->rows(3)
                    ->label('Descrição'),
                
                Select::make('status')
                    ->options([
                        'planned' => 'Planejado',
                        'in_progress' => 'Em Progresso',
                        'paused' => 'Pausado',
                        'completed' => 'Concluído',
                        'cancelled' => 'Cancelado',
                    ])
                    ->default('planned')
                    ->required()
                    ->label('Status'),
                
                DateTimePicker::make('expected_start_at')
                    ->label('Data Prevista de Início'),
                
                DateTimePicker::make('expected_end_at')
                    ->label('Data Prevista de Término'),
                
                Select::make('created_by')
                    ->relationship('creator', 'name')
                    ->searchable()
                    ->preload()
                    ->label('Criado por')
                    ->default(auth()->id()),
            ]);
    }
}
