<?php

namespace App\Filament\Resources\LeadsResource\Pages;

use App\Filament\Resources\LeadsResource;
use Filament\Resources\Pages\CreateRecord;

class CreateLeads extends CreateRecord
{
    protected static string $resource = LeadsResource::class;

    protected function getHeaderActions(): array
    {
        return [

        ];
    }
}
