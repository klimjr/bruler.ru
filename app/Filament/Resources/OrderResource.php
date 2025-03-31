<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Http\Controllers\DolyamiController;
use App\Models\Color;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Size;
use App\Models\StoreSetting;
use Carbon\Carbon;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Tabs;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Illuminate\Database\Eloquent\Collection;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use Filament\Forms\Components\ViewField;
use Filament\Forms\Components\Grid as FormGrid;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Grid as InfolistGrid;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Tables\Actions\ViewAction;
use Filament\Infolists\Components\ImageEntry;
use Filament\Forms\Components\Section as FormSection;
use Filament\Infolists\Components\Group;

class OrderResource extends Resource
{

    protected static ?string $modelLabel = 'Заказ';

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?string $pluralModelLabel = 'Заказы';


    protected static ?int $navigationSort = 3;

    protected static ?string $model = Order::class;

    protected static ?string $slug = 'orders';

    protected static ?string $recordTitleAttribute = 'id';

    public static function form(Form $form): Form
    {
        $model = $form->model;

        $select_variant_options = [];
        $newProducts = [];

        foreach ($model->products as $product) {

            $product_variants = ProductVariant::where('product_id', $product['id'])->where('amount', '>', '0')->get();

            switch ($product['type']) {
                case Product::TYPE_PRODUCT:
                    $variant = ProductVariant::find($product['variant']);
                    if ($variant) {
                        $product['image'] = $variant->image;
                        $product['article'] = $variant->article;
                    }
                    $newProducts[] = $product;
                    break;
                case Product::TYPE_CERTIFICATE:
                    $cert = Product::find($product['id']);
                    $product['image'] = $cert['image'];
                    $product['price'] = $product['certificate']['price'];
                    $newProducts[] = $product;
                    break;
            }

            if (count($product_variants) >= 1) {
                $variant_options = [];
                foreach ($product_variants as $variant) {
                    $color = Color::find($variant->color_id);
                    $size = Size::find($variant->size_id);
                    $variant_options[$variant->id] = "Цвет: $color->name. Размер: $size->name";
                }
                $select_variant_options[$product['name']] = $variant_options;
            } else {
                $products = Product::all();

                foreach ($products as $_product) {
                    $variants = $_product->variants()->where('amount', '>', 0)->get();
                    $variant_options = [];

                    foreach ($variants as $variant) {
                        $color = Color::find($variant->color_id);
                        $size = Size::find($variant->size_id);
                        $variant_options[$variant->id] = "Цвет: $color->name. Размер: $size->name";
                    }

                    $select_variant_options[$_product->name] = $variant_options;
                }
            }
        }

        $order = Order::find($form->model['id']);
        $order->update([
            'products' => count($newProducts) >= 1 ? $newProducts : $order->products
        ]);

        $storeSettings = StoreSetting::first();
        $paymentOptions = \Arr::mapWithKeys($storeSettings->events["payments"],fn($payment,$key) => [$key => $payment["label"]]);
        $deliveryOptions = \Arr::mapWithKeys($storeSettings->events["delivery"],fn($delivery,$key) => [$key => $delivery["label"]]);


        return $form->schema([

            Select::make('user_id')
                ->label('Пользователь')
                ->nullable()
                ->searchable()
                ->relationship(name: 'user', titleAttribute: 'name'),

            TextInput::make('country')
                ->label('Страна')
                ->readOnly(),

            TextInput::make('city')
                ->label('Город')
                ->readOnly(),

            TextInput::make('city_code')
                ->label('Код города (В СДЭК)')
                ->readOnly(),

            TextInput::make('address')
                ->label('Адрес'),

            TextInput::make('index')
                ->label('Индекс')
                ->numeric(),

            Select::make('delivery_type')
                ->label('Тип доставки')
                ->options($deliveryOptions),

            TextInput::make('comment')
                ->label('Комментарий'),

            FormSection::make('Информация о доставке')
                ->schema([
                    ViewField::make('delivery_info')
                        ->view('filament.resources.order-resource.delivery-info')
                ])
                ->visible(fn ($record) => $record && $record->delivery_type === 0)  // Показываем секцию только если delivery_type === 0
                ->collapsible(),

            TextInput::make('recipient_name')
                ->label('Имя'),

            TextInput::make('recipient_last_name')
                ->label('Фамилия'),

            TextInput::make('recipient_phone')
                ->label('Телефон'),

            TextInput::make('recipient_email')
                ->label('Email'),

            Select::make('payment_type')
                ->label('Тип оплаты')
                ->options($paymentOptions),

            TextInput::make('price_with_promocode')
                ->label('Стоимость (с промокодом)')
                ->readOnly()
                ->numeric(),

            TextInput::make('price_order')
                ->label('Стоимость заказа')
                ->readOnly()
                ->numeric(),

            TextInput::make('price')
                ->label('Стоимость (общая)')
                ->readOnly()
                ->numeric(),

            TextInput::make('delivery_price')
                ->label('Стоимость доставки')
                ->readOnly()
                ->numeric(),

            TextInput::make('promocode')
                ->label('Промокод')
                ->readOnly(),

            TextInput::make('points_amount')
                ->label('Количество использованных баллов')
                ->readOnly(),

            Select::make('status')
                ->label('Статус')
                ->options([
                    Order::STATUS_NOT_CONFIRMED => 'Не подтвержден',
                    Order::STATUS_CREATED => 'Новый',
                    Order::STATUS_CONFIRMED => 'Подтвержден',
                    Order::STATUS_PAID_RECEIPT => 'Оплата при получении',
                    Order::STATUS_PAID => 'Оплачен',
                    Order::STATUS_SHIPPING => 'К отгрузке',
                    Order::STATUS_SENT_TO_DELIVERY => 'Отправлен в доставку (проставьте дату доставки и время, а также трек номер)',
                    Order::STATUS_DELIVERED => 'Доставлен'
                ]),

            Select::make('payment_status')
                ->label('Статус оплаты')
                ->options([
                    Order::PAYMENT_STATUS_CREATED => 'Создан',
                    Order::PAYMENT_STATUS_PAID => 'Оплачен',
                    Order::PAYMENT_STATUS_REJECTED => 'Отклонен',
                ])
                ->nullable(),
//            Placeholder::make('paid_at')
//                ->label('Дата оплаты')
//                ->content(fn(?Order $record): string => $record?->paid_at?->diffForHumans() ?? '-'),

            TextInput::make('delivery_date')
                ->label('Дата доставки'),

            TextInput::make('delivery_time')
                ->label('Время доставки'),

            TextInput::make('track_number')
                ->label('Трек номер'),

            DateTimePicker::make('sent_at')
                ->label('Дата отправки заказа'),

            DateTimePicker::make('close_at')
                ->label('Дата закрытия заказа'),

            Placeholder::make('created_at')
                ->label('Дата создания заказа')
                ->content(fn(?Order $record): string => $record?->created_at ? $record->created_at->format('d.m.y') : '-'),

            Placeholder::make('updated_at')
                ->label('Дата обновления заказа')
                ->content(fn(?Order $record): string => $record?->updated_at ? $record->updated_at->format('d.m.y') : '-'),

            Placeholder::make('paid_at')
                ->label('Дата оплаты заказа')
                ->content(fn(?Order $record): string => $record?->paid_at ? $record->paid_at->format('d.m.y') : '-'),

            Placeholder::make('confirmation_at')
                ->label('Дата подтверждения заказа')
                ->content(function (?Order $record): string {
                    return $record?->confirmation_at ? Carbon::parse($record->confirmation_at)->format('d.m.y') : '-';
                }),
            FormSection::make('Информация о сертификате')
                ->schema([
                    TextInput::make('certificate')
                        ->label('Код сертификата'),
                    TextInput::make('cert_amount')
                        ->label('Сумма списания')
                        ->numeric(),
                ])
                ->visible(fn ($record) => $record && $record->use_certificate)
                ->collapsible()
                ->columnSpan(1),
            Tabs::make('Tabs')
                ->tabs([
                    Tabs\Tab::make('Товары')
                        ->schema([
                            Repeater::make('products')
                                ->label('Товары')
                                ->schema([
                                    FileUpload::make('image')
                                        ->label('Изображение')
                                        ->directory('product-variants'),

                                    FormGrid::make(9)
                                        ->extraAttributes(
                                            function ($get) {
                                                return [
                                                    'class' => 'product-grid-' . $get('id'),
                                                    'data-product' => $get('id')
                                                ];
                                            }
                                        )
                                        ->schema([
                                            Select::make('id')
                                                ->label('Товар')
                                                ->options(Product::all()->pluck('name_en', 'id'))
                                                ->searchable()
                                                ->columnSpan(8)
                                                ->extraAttributes(function ($get) {
                                                    return ['class' => 'product-select-' . $get('id')];
                                                }),
                                            ViewField::make('copy-button')
                                                ->view('components.copy-product')
                                                ->columnSpan(1)
                                        ]),

                                    Select::make('variant')
                                        ->label('Вариант')
                                        ->options($select_variant_options)
                                        ->searchable(),

                                    TextInput::make('price')
                                        ->label('Цена')
                                        ->readOnly()
                                        ->numeric(),

                                    TextInput::make('article')
                                        ->label('Артикул'),
                                    // ->readOnly()

                                    TextInput::make('quantity')
                                        ->label('Кол-во')
                                        ->numeric(),
                                ]),
                        ]),

                    Tabs\Tab::make('Товары в наборе')
                        ->schema([
                            ViewField::make('set-info')
                                ->view('components.set-info'),
                        ]),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        $storeSettings = StoreSetting::first();
        $paymentOptions = \Arr::mapWithKeys($storeSettings->events["payments"],fn($payment,$key) => [$key => $payment["label"]]);
        $deliveryOptions = \Arr::mapWithKeys($storeSettings->events["delivery"],fn($delivery,$key) => [$key => $delivery["label"]]);

        return $table
            ->modifyQueryUsing(function ($query) {
                return $query->whereNotNull('paid_at');
            })
            ->recordUrl(fn(Order $record): string => static::getUrl('edit', ['record' => $record]))
            ->defaultSort('id', 'desc')
            ->columns([
                TextColumn::make('id')
                    ->label('#')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Дата заказа')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Статус заказа')
                    ->formatStateUsing(function ($state): string {
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
                TextColumn::make('user.name')
                    ->description(fn(Order $record): string => $record->user?->last_name ?? '')
                    ->label('Пользователь')
                    ->sortable(),
                TextColumn::make('price')
                    ->money('RUB')
                    ->label('Цена')
                    ->sortable(),
//                TextColumn::make('country')
//                    ->label('Страна')
//                    ->sortable(),
                TextColumn::make('city')
                    ->label('Город')
                    ->sortable(),
//                TextColumn::make('city_code')
//                    ->label('Код города (В СДЭК)')
//                    ->sortable(),
                TextColumn::make('address')
                    ->label('Адрес')
                    ->sortable(),
//                TextColumn::make('products')
//                    ->label('Товары')
//                    ->formatStateUsing(function (array $state): string {
//                        $product_name = $state['name'];
//                        $product_price = $state['price'];
//                        $product_quantity = $state['quantity'];
//                        $product_variant = isset($state['variant']) ? ProductVariant::find($state['variant']) : 'null';
//                        $db_product_color = $product_variant instanceof ProductVariant ? Color::find($product_variant->color_id) : 'null';
//                        $db_product_size = $product_variant instanceof ProductVariant ? Size::find($product_variant->size_id) : 'null';
//                        $product_color = $db_product_color['name'] ?? 'null';
//                        $product_size = $db_product_size['name'] ?? 'null';
//                        $product_article = $product_variant instanceof ProductVariant ? $product_variant['article'] : 'null';
//                        return "$product_name ($product_article) - $product_price ₽ ($product_quantity шт.). Размер - $product_size. Цвет - $product_color";
//                    })
//                    ->listWithLineBreaks(),
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
                                fn(Builder $query, $price): Builder => $query->where('price', '<=', $price),
                            );
                    }),
                SelectFilter::make('user')
                    ->label('Пользователь')
                    ->relationship('user', 'name'),
                SelectFilter::make("payment_type")
                    ->label("Тип оплаты")
                    ->options($paymentOptions),
                SelectFilter::make("delivery_type")
                    ->label("Тип доставки")
                    ->options($deliveryOptions),


//                SelectFilter::make('payment_type')->options([
//                    Order::PAYMENT_TYPE_CARD => 'Карта',
//                    Order::PAYMENT_TYPE_DOLYAMI => 'Долями',
//                    Order::PAYMENT_TYPE_CASH => 'Наличные',
//                    Order::PAYMENT_TYPE_CRYPTO => 'Криптовалюта'
//                ])->label('Тип оплаты'),
                SelectFilter::make('status')
                    ->label('Статус')
                    ->options([
                        Order::STATUS_NOT_CONFIRMED => 'Не подтвержден',
                        Order::STATUS_CREATED => 'Новый',
                        Order::STATUS_CONFIRMED => 'Подтвержден',
                        Order::STATUS_PAID_RECEIPT => 'Оплата при получении',
                        Order::STATUS_PAID => 'Оплачен',
                        Order::STATUS_SHIPPING => 'К отгрузке',
                        Order::STATUS_SENT_TO_DELIVERY => 'Отправлен в доставку (проставьте дату доставки и время, а также трек номер)',
                        Order::STATUS_DELIVERED => 'Доставлен'
                    ])
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    BulkAction::make('Возврат-в-долях')
                        ->requiresConfirmation()
                        ->action(function (Collection $records) {
                            $order = Order::find($records[0]['id']);
                            $dolyame = new DolyamiController();
                            $dolyame->refundOrder($order);
                        }),
                    BulkAction::make('change_status_to_shipping')
                        ->label('Изменить статус на "К отгрузке"')
                        ->action(function (Collection $records): void {
                            $records->each(function ($record) {
                                $record->update(['status' => Order::STATUS_SHIPPING]);
                            });
                        })
                        ->requiresConfirmation()
                        ->deselectRecordsAfterCompletion()
                        ->icon('heroicon-o-truck'),
                    ExportBulkAction::make()
                ]),
            ])
            ->actions([
                \Filament\Tables\Actions\EditAction::make(),
                ViewAction::make()
                    ->icon('heroicon-o-eye')
                    ->color('success'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
            'view' => Pages\ViewOrder::route('/{record}'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return [];
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                InfolistGrid::make(2)
                    ->schema([
                        // Секция с информацией о клиенте
                        Section::make('Информация о клиенте')
                            ->icon('heroicon-o-user')
                            ->columnSpan(1)
                            ->schema([
                                TextEntry::make('user.name')
                                    ->label('Имя')
                                    ->icon('heroicon-o-user-circle'),
                                TextEntry::make('recipient_phone')
                                    ->label('Телефон')
                                    ->icon('heroicon-o-phone'),
                                TextEntry::make('recipient_email')
                                    ->label('Email')
                                    ->icon('heroicon-o-envelope'),
                                TextEntry::make('address')
                                    ->label('Адрес доставки')
                                    ->icon('heroicon-o-map-pin'),
                            ]),

                        // Секция с информацией о заказе
                        Section::make('Информация о заказе')
                            ->icon('heroicon-o-shopping-cart')
                            ->columnSpan(1)
                            ->schema([
                                TextEntry::make('id')
                                    ->label('Номер заказа')
                                    ->formatStateUsing(fn ($state) => "№{$state}")
                                    ->color('primary')
                                    ->size('lg')
                                    ->weight('bold'),
                                TextEntry::make('created_at')
                                    ->label('Дата заказа')
                                    ->dateTime('d.m.Y H:i'),
                                TextEntry::make('payment_type')
                                    ->label('Способ оплаты')
                                    ->formatStateUsing(function ($state) {
                                        $paymentTypes = StoreSetting::first()->events['payments'];
                                        return $paymentTypes[$state]['label'] ?? '-';
                                    }),
                                TextEntry::make('paid_at')
                                    ->label('Дата оплаты')
                                    ->formatStateUsing(function ($state) {
                                        return $state ? Carbon::parse($state)->format('d.m.Y H:i') : '-';
                                    }),
                                TextEntry::make('delivery_type')
                                    ->label('Способ доставки')
                                    ->formatStateUsing(function ($state) {
                                        $deliveryTypes = StoreSetting::first()->events['delivery'];
                                        return $deliveryTypes[$state]['label'] ?? '-';
                                    }),
                            ]),
                    ]),

                // Секция с товарами
                Section::make('Товары')
                    ->icon('heroicon-o-gift')
                    ->columnSpanFull()
                    ->schema([
                        \Filament\Infolists\Components\RepeatableEntry::make('products')
                            ->schema([
                                InfolistGrid::make(4)
                                    ->schema([
                                        ImageEntry::make('image')
                                            ->label('Изображение')
                                            ->columnSpan(1)
                                            ->square()
                                            ->height(100),
                                        TextEntry::make('name')
                                            ->label('Наименование')
                                            ->columnSpan(1),
                                        TextEntry::make('quantity')
                                            ->label('Количество')
                                            ->columnSpan(1),
                                        TextEntry::make('price')
                                            ->label('Цена')
                                            ->money('RUB')
                                            ->columnSpan(1),
                                    ])
                            ]),
                    ]),

                // New Certificate section
                Section::make('Информация о сертификате')
                    ->icon('heroicon-o-ticket')
                    ->columnSpanFull()
                    ->visible(fn (Order $record) => $record->type === \App\Models\Product::TYPE_CERTIFICATE || $record->use_certificate)
                    ->schema([
                        InfolistGrid::make(2)
                            ->schema([
                                Group::make([
                                    TextEntry::make('target_email')
                                        ->label('Email получателя сертификата'),
                                    TextEntry::make('certificate.price')
                                        ->label('Сумма сертификата')
                                        ->money('RUB'),
                                ])
                                    ->visible(fn (Order $record) => $record->type === \App\Models\Product::TYPE_CERTIFICATE),

                                Group::make([
                                    TextEntry::make('used_certificate')
                                        ->label('Использованный сертификат'),
                                    TextEntry::make('certificate')
                                        ->label('Код сертификата'),
                                    TextEntry::make('cert_amount')
                                        ->label('Сумма списания')
                                        ->money('RUB'),
                                ])
                                    ->visible(fn (Order $record) => $record->use_certificate),
                            ]),
                    ]),

                // Секция с итогами
                Section::make()
                    ->schema([
                        InfolistGrid::make(2)
                            ->schema([
                                TextEntry::make('delivery_price')
                                    ->label('Стоимость доставки')
                                    ->money('RUB'),
                                TextEntry::make('price')
                                    ->label('Итого')
                                    ->money('RUB')
                                    ->weight('bold')
                                    ->size('lg')
                                    ->color('success'),
                            ]),
                    ])
            ]);
    }
}
