<?php

namespace App\Filament\Resources\LookResource\Pages;

use App\Filament\Resources\LookResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditLook extends EditRecord
{
    protected static string $resource = LookResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
