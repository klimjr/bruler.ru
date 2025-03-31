<?php

namespace App\Filament\Resources\RunningTextResource\Pages;

use App\Filament\Resources\ColorResource;
use App\Filament\Resources\RunningTextResource;
use Filament\Resources\Pages\CreateRecord;

class CreateRunningText extends CreateRecord
{
    protected static string $resource = RunningTextResource::class;

    protected function getActions(): array
    {
        return [

        ];
    }
}
