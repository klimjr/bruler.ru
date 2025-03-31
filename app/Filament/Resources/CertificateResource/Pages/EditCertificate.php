<?php

namespace App\Filament\Resources\CertificateResource\Pages;

use App\Filament\Resources\CertificateResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\EditRecord;

class EditCertificate extends EditRecord
{
    protected static string $resource = CertificateResource::class;

    protected function getActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
