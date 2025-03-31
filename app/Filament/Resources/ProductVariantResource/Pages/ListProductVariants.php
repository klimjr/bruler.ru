<?php

namespace App\Filament\Resources\ProductVariantResource\Pages;

use App\Filament\Resources\ProductVariantResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListProductVariants extends ListRecords
{
    protected static string $resource = ProductVariantResource::class;

    protected function getActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
