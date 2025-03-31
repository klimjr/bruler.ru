<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use http\Client\Curl\User;
use Illuminate\Support\Facades\Log;
use pxlrbt\FilamentExcel\Actions\Pages\ExportAction;
use pxlrbt\FilamentExcel\Columns\Column;
use pxlrbt\FilamentExcel\Exports\ExcelExport;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ExportAction::make()->exports([
                ExcelExport::make()->withColumns([
                    Column::make('id')->heading('id'),
                    Column::make('name')->heading('Имя'),
                    Column::make('last_name')->heading('Фамилия'),
                    Column::make('email')->heading('E-mail'),
                    Column::make('phone')->heading('Телефон'),
                    Column::make('city')->heading('Город'),
                    Column::make('birthday')->heading('Дата рождения'),
                    Column::make('created_at')->heading('Кол-во заказов')->formatStateUsing(function ($record) {
                        $user = \App\Models\User::find($record->id);
                        return $user->orders->count();
                    }),
                ]),
            ]),
            Actions\CreateAction::make(),
        ];
    }
}
