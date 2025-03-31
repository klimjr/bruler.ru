<?php

namespace App\Filament\Resources\RunningTextResource\Pages;

use App\Filament\Resources\RunningTextResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListRunningText extends ListRecords
{
    protected static string $resource = RunningTextResource::class;

    protected function getActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
