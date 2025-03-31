<?php

namespace App\Filament\Resources\SizeResource\Pages;

use App\Filament\Resources\SizeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSizes extends ListRecords
{
    protected static string $resource = SizeResource::class;

    protected function getActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
