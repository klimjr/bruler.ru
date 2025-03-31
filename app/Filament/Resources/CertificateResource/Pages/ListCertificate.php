<?php

namespace App\Filament\Resources\CertificateResource\Pages;

use App\Filament\Resources\CertificateResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\ListRecords;

class ListCertificate extends ListRecords
{
    protected static string $resource = CertificateResource::class;

    protected function getActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
