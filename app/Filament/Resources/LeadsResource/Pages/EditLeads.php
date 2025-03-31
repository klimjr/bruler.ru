<?php

namespace App\Filament\Resources\LeadsResource\Pages;

use App\Filament\Resources\LeadsResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditLeads extends EditRecord
{
    protected static string $resource = LeadsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
