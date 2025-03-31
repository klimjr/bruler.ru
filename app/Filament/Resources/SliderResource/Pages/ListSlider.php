<?php

namespace App\Filament\Resources\SliderResource\Pages;

use App\Filament\Resources\ColorResource;
use App\Filament\Resources\SliderResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSlider extends ListRecords
{
    protected static string $resource = SliderResource::class;

    protected function getActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
