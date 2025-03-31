<?php

namespace App\Filament\Resources\ActionResource\Pages;

use App\Filament\Resources\ActionResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAction extends EditRecord
{
    protected static string $resource = ActionResource::class;

    protected static ?string $title = 'Редактировать акцию';
    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
