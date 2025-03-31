<?php

namespace App\Filament\Resources\LookResource\Pages;

use App\Filament\Resources\LookResource;
use Filament\Resources\Pages\CreateRecord;

class CreateLook extends CreateRecord
{
    protected static string $resource = LookResource::class;

    protected function getHeaderActions(): array
    {
        return [

        ];
    }
}
