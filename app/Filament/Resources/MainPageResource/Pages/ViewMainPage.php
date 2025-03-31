<?php

namespace App\Filament\Resources\MainPageResource\Pages;

use App\Filament\Resources\MainPageResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewMainPage extends ViewRecord
{
    protected static string $resource = MainPageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
