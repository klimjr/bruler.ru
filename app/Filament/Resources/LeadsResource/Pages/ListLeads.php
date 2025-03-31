<?php

namespace App\Filament\Resources\LeadsResource\Pages;

use App\Filament\Resources\LeadsResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListLeads extends ListRecords
{
    protected static string $resource = LeadsResource::class;

    protected function getHeaderActions(): array
    {
        return [
//            CreateAction::make(),
        ];
    }
}
