<?php

namespace App\Filament\Resources\ReadyMadeResource\Pages;

use App\Filament\Resources\ReadyMadeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListReadyMades extends ListRecords
{
    protected static string $resource = ReadyMadeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
