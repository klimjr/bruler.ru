<?php

namespace App\Filament\Resources\MainPageResource\Pages;

use App\Filament\Resources\MainPageResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMainPage extends EditRecord
{
    protected static string $resource = MainPageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
