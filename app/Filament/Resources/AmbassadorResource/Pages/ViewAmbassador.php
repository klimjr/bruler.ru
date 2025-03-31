<?php

namespace App\Filament\Resources\AmbassadorResource\Pages;

use App\Filament\Resources\AmbassadorResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewAmbassador extends ViewRecord
{
    protected static string $resource = AmbassadorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
