<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StoreSettingResource\Pages;
use App\Models\Product;
use App\Models\StoreSetting;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;

class StoreSettingResource extends Resource
{
    protected static ?string $modelLabel = 'Настройки магазина';

    protected static ?string $navigationGroup = 'Настройки';

    protected static ?int $navigationSort = 777;

    protected static ?string $pluralModelLabel = 'Настройки магазина';
    protected static ?string $model = StoreSetting::class;

    protected static ?string $slug = 'store_settings';

    protected static ?string $recordTitleAttribute = 'id';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Tabs::make('')
                ->tabs([
                    Tabs\Tab::make('Акции')
                        ->schema([
                            Checkbox::make('events.use_free_three_product')
                                ->label('При заказе 3 товар бесплатный'),
                            Select::make('events.exception_products')
                                ->multiple()
                                ->searchable()
                                ->label('Товары-исключения для акции 3 товара')
                                ->options([
                                    Product::all()->pluck('name_en', 'id')
                                        ->toArray()
                                ])
                        ]),
                    Tabs\Tab::make('Способы оплаты')
                        ->schema([
                            Repeater::make('events.payments')
                                ->reorderable(false)
                                ->label('Способы оплаты')
                                ->schema([
                                    Toggle::make('active')
                                        ->label('Активность'),
                                    TextInput::make('label')
                                        ->label('Заголовок')
                                        ->required(),
                                    TextInput::make('id')
                                        ->label('ID')
                                        ->required(),
                                    FileUpload::make('icon')
                                        ->label('Иконка'),
//                                        ->required(),
                                    FileUpload::make('icon_active')
                                        ->label('Иконка активная'),
//                                        ->required(),
                                    Textarea::make('description')
                                        ->label('Описание'),
                                ]),
                        ]),
                    Tabs\Tab::make('Способы доставки')
                        ->schema([
                            Repeater::make('events.delivery')
                                ->label('Способы оплаты')
                                ->reorderable(false)
                                ->schema([
                                    Toggle::make('active')
                                        ->label('Активность'),
                                    TextInput::make('label')
                                        ->label('Заголовок')
                                        ->required(),
                                    TextInput::make('id')
                                        ->label('ID')
                                        ->required(),
                                    Textarea::make('description')
                                        ->label('Описание'),
                                    TextInput::make('price')
                                        ->label('Стоимость доставки')
                                        ->default(0)
                                ]),
                        ])
                ]),
            Placeholder::make('created_at')
                ->label('Дата создания')
                ->content(fn(?StoreSetting $record): string => $record?->created_at?->diffForHumans() ?? '-'),

            Placeholder::make('updated_at')
                ->label('Дата обновления')
                ->content(fn(?StoreSetting $record): string => $record?->updated_at?->diffForHumans() ?? '-'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('id')
                ->label('#')
                ->sortable(),
            TextColumn::make('created_at')
                ->label('Настройки магазина')
                ->formatStateUsing(fn() => 'Настройки магазина')
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStoreSetting::route('/'),
            'create' => Pages\CreateStoreSetting::route('/create'),
            'edit' => Pages\EditStoreSetting::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['id'];
    }
}
