<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LeadsResource\Pages;
use App\Models\Color;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Size;
use App\Models\StoreSetting;
use Carbon\Carbon;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ViewField;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Columns\ButtonColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\RepeatableEntry;

class LeadsResource extends Resource
{
    protected static ?string $pluralModelLabel = "Лиды";



    protected static ?string $model = Order::class;
    protected static ?string $slug = "leads";

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationIcon = "heroicon-o-user-group";

    public static function form(Form $form): Form
    {
        $model = $form->model;

        $select_variant_options = [];
        $newProducts = [];

        foreach ($model->products as $product) {
            $product_variants = ProductVariant::where(
                "product_id",
                $product["id"]
            )
                ->where("amount", ">", "0")
                ->get();

            switch ($product["type"]) {
                case Product::TYPE_PRODUCT:
                    $variant = ProductVariant::find($product["variant"]);
                    if ($variant) {
                        $product["image"] = $variant->image;
                        $product["article"] = $variant->article;
                    }
                    $newProducts[] = $product;
                    break;
                case Product::TYPE_CERTIFICATE:
                    $cert = Product::find($product["id"]);
                    $product["image"] = $cert["image"];
                    $product["price"] = $product["certificate"]["price"];
                    $newProducts[] = $product;
                    break;
            }

            if (count($product_variants) >= 1) {
                $variant_options = [];
                foreach ($product_variants as $variant) {
                    $color = Color::find($variant->color_id);
                    $size = Size::find($variant->size_id);
                    $variant_options[
                        $variant->id
                    ] = "Цвет: $color->name. Размер: $size->name";
                }
                $select_variant_options[$product["name"]] = $variant_options;
            } else {
                $products = Product::all();

                foreach ($products as $_product) {
                    $variants = $_product
                        ->variants()
                        ->where("amount", ">", 0)
                        ->get();
                    $variant_options = [];

                    foreach ($variants as $variant) {
                        $color = Color::find($variant->color_id);
                        $size = Size::find($variant->size_id);
                        $variant_options[
                            $variant->id
                        ] = "Цвет: $color->name. Размер: $size->name";
                    }

                    $select_variant_options[$_product->name] = $variant_options;
                }
            }
        }

        $order = Order::find($form->model["id"]);
        $order->update([
            "products" =>
                count($newProducts) >= 1 ? $newProducts : $order->products,
        ]);

        $storeSettings = StoreSetting::first();
        $paymentOptions = \Arr::mapWithKeys($storeSettings->events["payments"],fn($payment,$key) => [$key => $payment["label"]]);
        $deliveryOptions = \Arr::mapWithKeys($storeSettings->events["delivery"],fn($delivery,$key) => [$key => $delivery["label"]]);

        return $form->schema([
            Select::make("user_id")
                ->label("Пользователь")
                ->nullable()
                ->searchable()
                ->relationship(name: "user", titleAttribute: "name"),

            TextInput::make("country")->label("Страна")->readOnly(),

            TextInput::make("city")->label("Город")->readOnly(),

            TextInput::make("city_code")
                ->label("Код города (В СДЭК)")
                ->readOnly(),

            TextInput::make("address")->label("Адрес"),

            TextInput::make("index")->label("Индекс")->numeric(),

            Select::make("delivery_type")
                ->label("Тип доставки")
                ->options($deliveryOptions),

            TextInput::make("comment")->label("Комментарий"),

            TextInput::make("recipient_name")->label("Имя"),

            TextInput::make("recipient_last_name")->label("Фамилия"),

            TextInput::make("recipient_phone")->label("Телефон"),

            TextInput::make("recipient_email")->label("Email"),

            Select::make("payment_type")
                ->label("Тип оплаты")
                ->options($paymentOptions),

            TextInput::make("price_with_promocode")
                ->label("Стоимость (с промокодом)")
                ->readOnly()
                ->numeric(),

            TextInput::make("price_order")
                ->label("Стоимость заказа")
                ->readOnly()
                ->numeric(),

            TextInput::make("price")
                ->label("Стоимость (общая)")
                ->readOnly()
                ->numeric(),

            TextInput::make("delivery_price")
                ->label("Стоимость доставки")
                ->readOnly()
                ->numeric(),

            TextInput::make("promocode")->label("Промокод")->readOnly(),

            TextInput::make("points_amount")
                ->label("Количество использованных баллов")
                ->readOnly(),

            Select::make("status")
                ->label("Статус")
                ->options([
                    Order::STATUS_NOT_CONFIRMED => "Не подтвержден",
                    Order::STATUS_CREATED => "Новый",
                    Order::STATUS_CONFIRMED => "Подтвержден",
                    Order::STATUS_PAID_RECEIPT => "Оплата при получении",
                    Order::STATUS_PAID => "Оплачен",
                    Order::STATUS_SHIPPING => "К отгрузке",
                    Order::STATUS_SENT_TO_DELIVERY =>
                        "Отправлен в доставку (проставьте дату доставки и время, а также трек номер)",
                    Order::STATUS_DELIVERED => "Доставлен",
                ]),

            Select::make("payment_status")
                ->label("Статус оплаты")
                ->options([
                    Order::PAYMENT_STATUS_CREATED => "Создан",
                    Order::PAYMENT_STATUS_PAID => "Оплачен",
                    Order::PAYMENT_STATUS_REJECTED => "Отклонен",
                ])
                ->nullable(),
            Placeholder::make("paid_at")
                ->label("Дата оплаты")
                ->content(
                    fn(
                        ?Order $record
                    ): string => $record?->paid_at?->diffForHumans() ?? "-"
                ),

            TextInput::make("delivery_date")->label("Дата доставки"),

            TextInput::make("delivery_time")->label("Время доставки"),

            TextInput::make("track_number")->label("Трек номер"),

            DateTimePicker::make("sent_at")->label("Дата отправки заказа"),

            DateTimePicker::make("close_at")->label("Дата закрытия заказа"),

            Placeholder::make("created_at")
                ->label("Дата создания заказа")
                ->content(
                    fn(?Order $record): string => $record?->created_at
                        ? $record->created_at->format("d.m.y")
                        : "-"
                ),

            Placeholder::make("updated_at")
                ->label("Дата обновления заказа")
                ->content(
                    fn(?Order $record): string => $record?->updated_at
                        ? $record->updated_at->format("d.m.y")
                        : "-"
                ),

            Placeholder::make("paid_at")
                ->label("Дата оплаты заказа")
                ->content(
                    fn(?Order $record): string => $record?->paid_at
                        ? $record->paid_at->format("d.m.y")
                        : "-"
                ),

            Placeholder::make("confirmation_at")
                ->label("Дата подтверждения заказа")
                ->content(function (?Order $record): string {
                    return $record?->confirmation_at
                        ? Carbon::parse($record->confirmation_at)->format(
                            "d.m.y"
                        )
                        : "-";
                }),

            Tabs::make("Tabs")->tabs([
                Tabs\Tab::make("Товары")->schema([
                    Repeater::make("products")
                        ->label("Товары")
                        ->schema([
                            FileUpload::make("image")
                                ->label("Изображение")
                                ->directory("product-variants"),

                            Grid::make(9)
                                ->extraAttributes(function ($get) {
                                    return [
                                        "class" => "product-grid-" . $get("id"),
                                        "data-product" => $get("id"),
                                    ];
                                })
                                ->schema([
                                    Select::make("id")
                                        ->label("Товар")
                                        ->options(
                                            Product::all()->pluck(
                                                "name_en",
                                                "id"
                                            )
                                        )
                                        ->searchable()
                                        ->columnSpan(8)
                                        ->extraAttributes(function ($get) {
                                            return [
                                                "class" =>
                                                    "product-select-" .
                                                    $get("id"),
                                            ];
                                        }),
                                    ViewField::make("copy-button")
                                        ->view("components.copy-product")
                                        ->columnSpan(1),
                                ]),

                            Select::make("variant")
                                ->label("Вариант")
                                ->options($select_variant_options)
                                ->searchable(),

                            TextInput::make("price")
                                ->label("Цена")
                                ->readOnly()
                                ->numeric(),

                            TextInput::make("article")->label("Артикул"),
                            // ->readOnly()

                            TextInput::make("quantity")
                                ->label("Кол-во")
                                ->numeric(),
                        ]),
                ]),
                Tabs\Tab::make("Товары в наборе")->schema([
                    ViewField::make("set-info")->view("components.set-info"),
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
                return $query->whereNull('paid_at');
            })
            ->defaultSort("id", "desc")
            ->filtersLayout(FiltersLayout::AboveContent)
            ->headerActions([
                Action::make('toggle_paid')
                    ->label('Показать возможно оплаченные заказы')
                    ->button()
                    ->icon('heroicon-m-sparkles')
                    ->color(fn ($livewire) => !empty($livewire->tableFilters['show_paid']['value']) ? 'primary' : 'gray')
                    ->action(function ($livewire): void {
                        $filters = $livewire->tableFilters;
                        if (!empty($filters['show_paid']['value'])) {
                            $filters['show_paid'] = ['value' => null];
                        } else {
                            $filters['show_paid'] = ['value' => true];
                        }
                        $livewire->tableFilters = $filters;
                        $livewire->resetPage();
                    }),
            ])
            ->columns([
                TextColumn::make("id")->label("#")->sortable(),

                TextColumn::make("status")->label("Статус заказа"),
                TextColumn::make("payment_status")->label("Статус оплаты"),

                TextColumn::make("created_at")
                    ->dateTime("d.m.Y H:i:s")
                    ->label("Дата"),

                TextColumn::make("payment_type")
                    ->label("Тип оплаты")
                    ->formatStateUsing(function ($state, $record): string {
                        $paymentTypes = StoreSetting::first()->events[
                            "payments"
                        ];
                        foreach ($paymentTypes as $key => $paymentType) {
                            if ($key == $state) {
                                if (
                                    $key == 3 &&
                                    $record->created_at <
                                        Carbon::parse("2025-02-14")
                                ) {
                                    return "Криптовалюта";
                                }
                                return $paymentType["label"];
                            }
                        }
                        return "-";
                    }),
            ])
            ->filters([
                TernaryFilter::make('show_paid')
                    ->label('Оплаченные заказы')
                    ->queries(
                        true: fn (Builder $query) => $query
                            ->whereNull('paid_at')
                            ->where('payment_type', 2)
                            ->where('payment_url', 'like', 'https://pay.ya.ru%'),
                        false: fn (Builder $query) => $query->whereNull('paid_at'),
                        blank: fn (Builder $query) => $query->whereNull('paid_at'),
                    )->default(false),
                SelectFilter::make("payment_type")
                    ->label("Тип оплаты")
                    ->options($paymentOptions),
                SelectFilter::make("delivery_type")
                    ->label("Тип доставки")
                    ->options($deliveryOptions),
            ])
            ->actions([
                EditAction::make(),
                // DeleteAction::make(),
                ViewAction::make()
                    ->icon('heroicon-o-eye')
                    ->color('success'),
                Action::make('convert_to_order')
                    ->label('Перевести в заказ')
                    ->button()
                    ->color(fn ($record) => !is_null($record->paid_at) ? 'success' : 'gray')
                    ->icon('heroicon-o-arrow-right-circle')
                    ->action(function ($record) {
                        $record->update([
                            'paid_at' => $record->created_at->format('Y-m-d H:i:s'),
                            'payment_status' => 'paid',
                            'status' => 'paid'
                        ]);
                    }),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    // DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLeads::route('/'),
            'create' => Pages\CreateLeads::route('/create'),
            'edit' => Pages\EditLeads::route('/{record}/edit'),
            'view' => Pages\ViewLeads::route('/{record}'),
        ];
    }

    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()->with(["user"]);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ["user.name"];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        $details = [];

        if ($record->user) {
            $details["User"] = $record->user->name;
        }

        return $details;
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                \Filament\Infolists\Components\Grid::make(2)
                    ->schema([
                        Section::make('Информация о клиенте')
                            ->icon('heroicon-o-user')
                            ->columnSpan(1)
                            ->schema([
                                TextEntry::make('user.name')
                                    ->label('Имя')
                                    ->icon('heroicon-o-user-circle'),
                                TextEntry::make('phone')
                                    ->label('Телефон')
                                    ->icon('heroicon-o-phone'),
                                TextEntry::make('email')
                                    ->label('Email')
                                    ->icon('heroicon-o-envelope'),
                                TextEntry::make('address')
                                    ->label('Адрес доставки')
                                    ->icon('heroicon-o-map-pin'),
                            ]),

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
                                TextEntry::make('delivery_type')
                                    ->label('Способ доставки')
                                    ->formatStateUsing(function ($state) {
                                        $deliveryTypes = StoreSetting::first()->events['delivery'];
                                        return $deliveryTypes[$state]['label'] ?? '-';
                                    }),
                            ]),
                    ]),

                Section::make('Товары')
                    ->icon('heroicon-o-gift')
                    ->columnSpanFull()
                    ->schema([
                        RepeatableEntry::make('products')
                            ->schema([
                                \Filament\Infolists\Components\Grid::make(4)
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

                Section::make()
                    ->schema([
                        \Filament\Infolists\Components\Grid::make(2)
                            ->schema([
                                TextEntry::make('delivery_price')
                                    ->label('Стоимость доставки')
                                    ->money('RUB'),
                                TextEntry::make('total_price')
                                    ->label('Итого')
                                    ->money('RUB')
                                    ->weight('bold')
                                    ->size('lg')
                                    ->color('success'),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
