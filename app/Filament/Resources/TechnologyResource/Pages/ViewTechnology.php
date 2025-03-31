<?php

namespace App\Filament\Resources\TechnologyResource\Pages;

use App\Filament\Resources\TechnologyResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewTechnology extends ViewRecord
{
    protected static string $resource = TechnologyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
