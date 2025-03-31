<?php

namespace App\Filament\Resources\AmbassadorResource\Pages;

use App\Filament\Resources\AmbassadorResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAmbassador extends EditRecord
{
    protected static string $resource = AmbassadorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
