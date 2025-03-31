<?php

namespace App\Filament\Resources\UserResource;

use App\Http\Controllers\DolyamiController;
use App\Models\Color;
use App\Models\Order;
use App\Models\ProductVariant;
use App\Models\Size;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class OrdersRelationshipManager extends RelationManager {

    protected static string $relationship = 'orders';

    public function form(Form $form): Form
    {
        return $form
            ->schema([

            ]);
    }

    public function table(Table $table): Table
    {
        TextColumn::configureUsing(function (TextColumn $column): void {
            $column
                ->toggleable()
                ->searchable();
        });
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('#')
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Статус')
                    ->sortable()
                    ->formatStateUsing(function ($state): string {
                        return match ($state) {
                            Order::STATUS_CREATED => 'Новый',
                            Order::STATUS_CONFIRMED => 'Подтвержден',
                            Order::STATUS_PAID => 'Оплачен',
                            Order::STATUS_SENT_TO_DELIVERY => 'Отправлен в доставку',
                            Order::STATUS_DELIVERED => 'Доставлен',
                            default => $state,
                        };
                    }),
                TextColumn::make('user.name')
                    ->description(fn(Order $record): string => $record->user?->last_name ?? '')
                    ->label('Пользователь')
                    ->sortable(),
                TextColumn::make('price')
                    ->label('Цена')
                    ->sortable(),
                TextColumn::make('country')
                    ->label('Страна')
                    ->sortable(),
                TextColumn::make('city')
                    ->label('Город')
                    ->sortable(),
                TextColumn::make('city_code')
                    ->label('Код города (В СДЭК)')
                    ->sortable(),
                TextColumn::make('address')
                    ->label('Адрес')
                    ->sortable(),
                TextColumn::make('products')
                    ->label('Товары')
                    ->formatStateUsing(function (array $state): string {
                        $product_name = $state['name'];
                        $product_price = $state['price'];
                        $product_quantity = $state['quantity'];
                        $product_variant = isset($state['variant']) ? ProductVariant::find($state['variant']) : 'null';
                        $db_product_color = $product_variant instanceof ProductVariant ? Color::find($product_variant->color_id) : 'null';
                        $db_product_size = $product_variant instanceof ProductVariant ? Size::find($product_variant->size_id) : 'null';
                        $product_color = $db_product_color['name'] ?? 'null';
                        $product_size = $db_product_size['name'] ?? 'null';
                        $product_article = $product_variant instanceof ProductVariant ? $product_variant['article'] : 'null';
                        $returnedProducts = "$product_name ($product_article) - $product_price ₽ ($product_quantity шт.). Размер - $product_size. Цвет - $product_color";

                        return $returnedProducts;
                    })
                    ->listWithLineBreaks(),
                TextColumn::make('created_at')
                    ->label('Дата создания заказа')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Filter::make('price')
                    ->form([
                        TextInput::make('filtered_price')->numeric()->label('Цена для сортировки (если цена меньше либо равна)')
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['filtered_price'],
                                fn (Builder $query, $price): Builder => $query->where('price', '<=', $price),
                            );
                    }),
                SelectFilter::make('status')->options([
                    Order::STATUS_CREATED => 'Новый',
                    Order::STATUS_CONFIRMED => 'Подтвержден',
                    Order::STATUS_PAID => 'Оплачен',
                    Order::STATUS_SENT_TO_DELIVERY => 'Отправлен в доставку (проставьте дату доставки и время, а также трек номер)',
                    Order::STATUS_DELIVERED => 'Доставлен'
                ])
            ])
            ->defaultSort('created_at', 'desc');
    }
}
