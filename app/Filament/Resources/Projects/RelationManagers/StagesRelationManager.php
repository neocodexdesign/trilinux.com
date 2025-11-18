<?php

namespace App\Filament\Resources\Projects\RelationManagers;

use Filament\Actions\AssociateAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DissociateAction;
use Filament\Actions\DissociateBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DateTimePicker;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use App\Models\User;
use App\Models\Stage;

class StagesRelationManager extends RelationManager
{
    protected static string $relationship = 'stages';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->label('Nome'),

                Textarea::make('description')
                    ->columnSpanFull()
                    ->rows(3)
                    ->label('Descrição'),

                Select::make('status')
                    ->options([
                        'planned' => 'Planejado',
                        'in_progress' => 'Em Progresso',
                        'paused' => 'Pausado',
                        'completed' => 'Completado',
                        'cancelled' => 'Cancelado',
                    ])
                    ->default('planned')
                    ->required()
                    ->label('Status'),

                Select::make('responsible_id')
                    ->relationship('responsible', 'name')
                    ->searchable()
                    ->preload()
                    ->label('Usuário Responsável')
                    ->helperText('Atribua a um usuário OU a uma equipe, nunca ambos')
                    ->reactive()
                    ->afterStateUpdated(fn ($state, callable $set) => $state ? $set('team_id', null) : null),

                Select::make('team_id')
                    ->relationship('team', 'name')
                    ->searchable()
                    ->preload()
                    ->label('Equipe Responsável')
                    ->helperText('Atribua a uma equipe OU a um usuário, nunca ambos')
                    ->reactive()
                    ->afterStateUpdated(fn ($state, callable $set) => $state ? $set('responsible_id', null) : null),

                Select::make('dependent_stage_id')
                    ->label('Depende da Etapa')
                    ->options(function () {
                        return Stage::where('project_id', $this->getOwnerRecord()->id)
                            ->pluck('name', 'id');
                    })
                    ->searchable()
                    ->helperText('Esta etapa só poderá iniciar após a conclusão da etapa selecionada'),

                DateTimePicker::make('expected_start_at')
                    ->label('Início Esperado'),

                DateTimePicker::make('expected_end_at')
                    ->label('Fim Esperado'),

                TextInput::make('order')
                    ->numeric()
                    ->default(0)
                    ->label('Ordem'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('order')
                    ->sortable()
                    ->label('#'),

                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->label('Nome'),

                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'planned' => 'Planejado',
                        'in_progress' => 'Em Progresso',
                        'paused' => 'Pausado',
                        'completed' => 'Completado',
                        'cancelled' => 'Cancelado',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'planned' => 'gray',
                        'in_progress' => 'info',
                        'paused' => 'warning',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                        default => 'gray',
                    })
                    ->label('Status'),

                TextColumn::make('responsible.name')
                    ->badge()
                    ->color('info')
                    ->label('Responsável')
                    ->default('—'),

                TextColumn::make('team.name')
                    ->badge()
                    ->color(fn ($record) => $record->team?->color ?? 'gray')
                    ->label('Equipe')
                    ->default('—'),

                TextColumn::make('expected_start_at')
                    ->dateTime('d/m/Y')
                    ->label('Início Esperado')
                    ->toggleable(),

                TextColumn::make('expected_end_at')
                    ->dateTime('d/m/Y')
                    ->label('Fim Esperado')
                    ->toggleable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make(),
                AssociateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DissociateAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DissociateBulkAction::make(),
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('order');
    }
}
