<?php

namespace App\Filament\Resources\PromocodesResource;

use App\Filament\Resources\ProductVariantResource;
use App\Filament\Resources\PromocodesResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPromocodes extends EditRecord
{
  protected static string $resource = PromocodesResource::class;

  protected function getActions(): array
  {
    return [
      DeleteAction::make(),
    ];
  }
}
