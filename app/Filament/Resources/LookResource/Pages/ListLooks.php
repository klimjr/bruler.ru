<?php

namespace App\Filament\Resources\LookResource\Pages;

use App\Filament\Resources\LookResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListLooks extends ListRecords
{
    protected static string $resource = LookResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
