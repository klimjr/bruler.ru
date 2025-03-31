<?php

namespace App\Filament\Resources\ActionResource\Pages;

use App\Filament\Resources\ActionResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAction extends CreateRecord
{
    protected static string $resource = ActionResource::class;

    protected static ?string $title = 'Создать акцию';

    protected function getHeaderActions(): array
    {
        return [

        ];
    }
}
