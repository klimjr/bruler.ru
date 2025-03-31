<?php

namespace App\Filament\Resources\RunningTextResource\Pages;

use App\Filament\Resources\RunningTextResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditRunningText extends EditRecord
{
    protected static string $resource = RunningTextResource::class;

    protected function getActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
