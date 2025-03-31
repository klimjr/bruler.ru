<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use Filament\Resources\Pages\CreateRecord;
use Spatie\ImageOptimizer\OptimizerChainFactory;
use App\Http\Controllers\XmlController;

class CreateProduct extends CreateRecord
{
    protected static string $resource = ProductResource::class;

    protected function getActions(): array
    {
        return [

        ];
    }

    protected function afterCreate(): void
    {
        $xml = new XmlController();
        $xml->generateXml();
        $xml->generateXml(true);
    }
}
