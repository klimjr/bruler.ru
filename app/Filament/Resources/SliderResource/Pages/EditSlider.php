<?php

namespace App\Filament\Resources\SliderResource\Pages;

use App\Filament\Resources\ColorResource;
use App\Filament\Resources\SliderResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSlider extends EditRecord
{
    protected static string $resource = SliderResource::class;

    protected function getActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
