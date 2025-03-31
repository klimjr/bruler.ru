<?php

namespace App\Filament\Resources\PromocodesResource;

use App\Filament\Resources\PromocodesResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Pages\Actions\ButtonAction;
use Filament\Notifications\Notification;

class ListPromocodes extends ListRecords
{
  protected static string $resource = PromocodesResource::class;

  protected function getActions(): array
  {
    return [
      ButtonAction::make('activateAll')
        ->label('Активировать все промокоды')
        ->action(fn() => $this->activateAllPromocodes())
        ->color('success'),
      ButtonAction::make('deactivateAll')
        ->label('Деактивировать все промокоды')
        ->action(fn() => $this->deactivateAllPromocodes())
        ->color('danger'),
      CreateAction::make()
    ];
  }

  protected function activateAllPromocodes()
  {
    PromocodesResource::activateAllPromocodes();
    Notification::make()
      ->title('Все промокоды успешно активированы')
      ->success()
      ->send();
  }

  protected function deactivateAllPromocodes()
  {
    PromocodesResource::deactivateAllPromocodes();
    Notification::make()
      ->title('Все промокоды успешно деактивированы')
      ->success()
      ->send();
  }
}
