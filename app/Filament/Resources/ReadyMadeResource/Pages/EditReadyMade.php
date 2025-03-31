<?php

namespace App\Filament\Resources\ReadyMadeResource\Pages;

use App\Filament\Resources\ReadyMadeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditReadyMade extends EditRecord
{
    protected static string $resource = ReadyMadeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
