<?php

namespace App\Filament\Tenant\Resources\Stages\Pages;

use App\Filament\Tenant\Resources\Stages\StageResource;
use Filament\Resources\Pages\CreateRecord;

class CreateStage extends CreateRecord
{
    protected static string $resource = StageResource::class;
}
