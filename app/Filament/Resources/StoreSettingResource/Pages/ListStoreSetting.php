<?php

namespace App\Filament\Resources\StoreSettingResource\Pages;

use App\Filament\Resources\StoreSettingResource;
use App\Models\StoreSetting;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListStoreSetting extends ListRecords
{
    protected static string $resource = StoreSettingResource::class;

    protected function getActions(): array
    {
        $existsSettings = StoreSetting::first();
        if ($existsSettings) return [];
        else return [
            CreateAction::make(),
        ];
    }
}
