<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use App\Http\Controllers\XmlController;

class EditProduct extends EditRecord
{
    protected static string $resource = ProductResource::class;

    protected function getActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        $xml = new XmlController();
        $xml->generateXml();
        $xml->generateXml(true);
    }
}
