<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use App\Models\Order;
use App\Models\StoreSetting;
use Filament\Resources\Pages\ListRecords;
use pxlrbt\FilamentExcel\Actions\Pages\ExportAction;
use pxlrbt\FilamentExcel\Columns\Column;
use pxlrbt\FilamentExcel\Exports\ExcelExport;
use Illuminate\Database\Eloquent\Builder;

class ListOrders extends ListRecords
{
    protected static string $resource = OrderResource::class;

    protected function getActions(): array
    {
        return [
            ExportAction::make()->exports([
                ExcelExport::make()
                    ->modifyQueryUsing(fn (Builder $query) => $query->whereNotNull('paid_at'))
                    ->withColumns([
                        Column::make('id')->heading('Номер заказа'),
                        Column::make('status')->heading('Статус заказа')->formatStateUsing(function ($state) {
                            return match ($state) {
                                Order::STATUS_NOT_CONFIRMED => 'Не подтвержден',
                                Order::STATUS_CREATED => 'Новый',
                                Order::STATUS_CONFIRMED => 'Подтвержден',
                                Order::STATUS_PAID_RECEIPT => 'Оплата при получении',
                                Order::STATUS_PAID => 'Оплачен',
                                Order::STATUS_SHIPPING => 'К отгрузке',
                                Order::STATUS_SENT_TO_DELIVERY => 'Отправлен в доставку',
                                Order::STATUS_DELIVERED => 'Доставлен',
                                default => $state,
                            };
                        }),
                        Column::make('recipient_name')
                            ->heading('ФИО')
                            ->formatStateUsing(function ($state, $record) {
                                // Сначала пробуем данные получателя
                                $firstName = $record->recipient_name;
                                $lastName = $record->recipient_last_name;

                                // Если данных получателя нет, используем данные пользователя
                                if (empty($firstName) && empty($lastName)) {
                                    $firstName = $record->user?->name;
                                    $lastName = $record->user?->last_name;
                                }

                                $fullName = trim(($firstName ?? '') . ' ' . ($lastName ?? ''));
                                return $fullName ?: '-';
                            }),
                        Column::make('user.email')->heading('Email'),
                        Column::make('user.phone')->heading('Телефон'),
                        Column::make('payment_type')
                            ->heading('Тип оплаты')
                            ->formatStateUsing(function ($state) {
                                $storeSettings = StoreSetting::first();
                                return $storeSettings->events['payments'][$state]['label'] ?? '-';
                            }),
                        Column::make('price')->heading('Оплаченная сумма'),
                        Column::make('delivery_price')->heading('Стоимость доставки'),
                        Column::make('promocode')->heading('Промокод'),
                        Column::make('used_points')->heading('Использованные баллы'),
                        Column::make('used_certificate')->heading('Использованный сертификат'),
                        Column::make('city')->heading('Город'),
                        Column::make('delivery_type')
                            ->heading('Тип доставки')
                            ->formatStateUsing(function ($state) {
                                $storeSettings = StoreSetting::first();
                                return $storeSettings->events['delivery'][$state]['label'] ?? '-';
                            }),
                        Column::make('pvz_code')->heading('Код ПВЗ (СДЭК)'),
                        Column::make('address')->heading('Адрес (СДЭК курьер, достависта)'),
                        Column::make('products')->heading('Товары')->formatStateUsing(function ($record) {
                            return implode(', ', array_map(function($product) {
                                return $product['name'];
                            }, $record->products));
                        }),
                        Column::make('created_at')->heading('Дата создания заказа')->formatStateUsing(function ($record) {
                            return $record->created_at->format('d.m.Y H:i:s');
                        }),
                    ]),
            ]),
        ];
    }
}
