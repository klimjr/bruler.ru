<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Category;
use App\Models\Collection;
use App\Models\Technology;
use App\Models\Color;
use App\Models\Product;
use App\Models\Size;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Get;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Form;
use Filament\Resources\Components\Tab;
use Filament\Resources\Resource;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Log;

class ProductResource extends Resource
{
    protected static ?string $modelLabel = 'Товар';

    protected static ?int $navigationSort = 777;
    protected static ?string $navigationGroup = 'Товары';

    protected static ?string $pluralModelLabel = 'Товары';

    protected static ?string $model = Product::class;

    protected static ?string $slug = 'products';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Tabs::make('')
                ->tabs([
                    Tabs\Tab::make('Главное')
                        ->schema([
                            TextInput::make('slug')
                                ->label('Значение для URL (slug)')
                                ->helperText('Необходимо указать уникальное наименование товара на латинице в одно слово, например: hoodie')
                                ->prefix('https://bruler.ru/категория/')
                                ->rule('regex:/^[a-zA-Z]+$/')
                                ->required(),
                            TextInput::make('name')
                                ->required(),
                            TextInput::make('name_en')
                                ->required(),

                            Select::make('type')
                                ->label('Тип товара')
                                ->options([
                                    Product::TYPE_PRODUCT => 'Товар',
                                    Product::TYPE_SET => 'Набор',
                                    Product::TYPE_CERTIFICATE => 'Сертификат'
                                ])
                                ->required()
                                ->reactive(),

                            TextInput::make('price')
                                ->required()
                                ->default(0)
                                ->numeric()
                                ->disabled(fn(Get $get) => $get('type') === Product::TYPE_SET)
                                ->reactive(),

                            TextInput::make('discount')
                                ->minValue(0)
                                ->maxValue(100)
                                ->numeric()
                                ->disabled(fn(Get $get) => $get('type') === Product::TYPE_SET)
                                ->reactive(),

                            TextInput::make('final_price')
                                ->label('Конечная цена')
                                ->numeric(),

                            Checkbox::make('preorder')
                                ->label('Товар по предзаказу?'),

                            Checkbox::make('new')
                                ->label('Новинка?'),

                            TextInput::make('position')
                                ->label('Порядковый номер в списке')
                                ->numeric()
                                ->required(),

                            FileUpload::make('image')
                                ->label('Превью')
                                ->required()
                                ->directory('products'),

                            FileUpload::make('back_img')
                                ->label('Превью при наведении')
                                ->directory('products'),

                            Select::make('category_id')
                                ->label('Категория')
                                ->live()
                                ->options(
                                    Category::all()->pluck('name', 'id')
                                        ->toArray()
                                )
                                ->required(),

                            Select::make('collection')
                                ->label('Коллекция')
                                ->options(
                                    Collection::all()->pluck('title', 'id')
                                        ->toArray()
                                ),

                            Select::make('technology_id')
                                ->label('Технология изготовления')
                                ->options(
                                    Technology::all()->pluck('name', 'id')
                                        ->toArray()
                                ),

                            TextInput::make('classifier')
                                ->label('Классификатор для связывания товаров'),

                            DateTimePicker::make('release_date')
                                ->label('Дата релиза (для отсчёта)'),
                            Toggle::make('show')
                                ->default(true)
                                ->label('Показывать на сайте?'),
                        ]),
                    Tabs\Tab::make('Текст')
                        ->schema([
                            Textarea::make('description')
                                ->label('Описание')
                                ->required(),
                        ]),
                    Tabs\Tab::make('Галерея')
                        ->schema([
                            Repeater::make('gallery')
                                ->label('Галерея')
                                ->schema([
                                    Select::make('color_id')
                                        ->label('Цвет')
                                        ->options(
                                            Color::pluck('name', 'id')
                                                ->toArray()
                                        )
                                        ->required(),

                                    Textarea::make('description')
                                        ->label('Описание')
                                        ->required(),

                                    Repeater::make('images')
                                        ->label('Галерея')
                                        ->schema([
                                            FileUpload::make('image')
                                                ->label('Изображение')
                                                ->reorderable()
                                                ->required()
                                                ->directory('products'),

                                            Textarea::make('alt')
                                                ->label('Alt')
                                                ->required(),
                                        ]),
                                ]),
                        ]),
                    Tabs\Tab::make('Размерная сетка')
                        ->schema([
                            FileUpload::make('mockup')
                                ->label('Мокап')
                                ->directory('products'),
                            Repeater::make('size_chart')
                                ->label('Размерная сетка')
                                ->schema([
                                    Select::make('size')
                                        ->label('Размер')
                                        ->options(
                                            Size::pluck('name', 'size')
                                                ->toArray()
                                        )
                                        ->required(),
                                    TextInput::make('chest_girth')
                                        ->label('Обхват груди')
                                        ->hidden(function (Get $get) use ($form): bool {
                                            $category_id = is_object($form->model) ? $form->model->category_id : null;
                                            if (is_null($category_id))
                                                return false;
                                            $category = Category::where('id', $category_id)->where('slug', 'pants')->first();
                                            return isset($category);
                                        })
                                        ->numeric(),
                                    TextInput::make('waist_girth') // Работает только для category->slug === 'pants'
                                    ->label('Обхват талии')
                                        ->hidden(function (Get $get) use ($form): bool {
                                            $category_id = is_object($form->model) ? $form->model->category_id : null;
                                            if (is_null($category_id))
                                                return false;
                                            $category = Category::where('id', $category_id)->where('slug', 'pants')->first();
                                            return !isset($category);
                                        })
                                        ->numeric(),
                                    TextInput::make('hip_girth')
                                        ->label('Обхват бедер')
                                        ->numeric(),
                                    TextInput::make('inner_seam_length') // Работает только для category->slug === 'pants'
                                    ->label('Длинна по внутреннему шву')
                                        ->hidden(function (Get $get) use ($form): bool {
                                            $category_id = is_object($form->model) ? $form->model->category_id : null;
                                            if (is_null($category_id))
                                                return false;
                                            $category = Category::where('id', $category_id)->where('slug', 'pants')->first();
                                            return !isset($category);
                                        })
                                        ->numeric(),
                                    TextInput::make('sleeve_length')
                                        ->label('Длина рукава')
                                        ->hidden(function (Get $get) use ($form): bool {
                                            $category_id = is_object($form->model) ? $form->model->category_id : null;
                                            if (is_null($category_id))
                                                return false;
                                            $category = Category::where('id', $category_id)->where('slug', 'pants')->first();
                                            return isset($category);
                                        })
                                        ->numeric(),
                                    TextInput::make('product_length')
                                        ->label('Длина изделия')
                                        ->numeric(),
                                ]),
                        ]),

                    Tabs\Tab::make('Варианты')
                        ->schema([
                            Repeater::make('variants')
                                ->label('Варианты')
                                ->relationship()
                                ->schema([
                                    Select::make('color_id')
                                        ->label('Цвет')
                                        ->options(
                                            Color::pluck('name', 'id')
                                                ->toArray()
                                        )
                                        ->required(),
                                    Select::make('size_id')
                                        ->label('Размер')
                                        ->options(
                                            Size::pluck('name', 'id')
                                                ->toArray()
                                        )
                                        ->required(),
                                    TextInput::make('amount')
                                        ->label('Остаток')
                                        ->numeric()
                                        ->required(),

                                    TextInput::make('article')
                                        ->label('Артикул')
                                        ->required(),

                                    TextInput::make('length')
                                        ->label('Длина (см.)')
                                        ->required()
                                        ->integer(),

                                    TextInput::make('width')
                                        ->label('Ширина (см.)')
                                        ->required()
                                        ->integer(),

                                    TextInput::make('height')
                                        ->label('Высота (см.)')
                                        ->required()
                                        ->integer(),

                                    TextInput::make('weight')
                                        ->label('Вес (гр.)')
                                        ->required()
                                        ->integer(),

                                    FileUpload::make('image')
                                        ->required()
                                        ->directory('product-variants'),
                                ]),
                        ]),

                    Tabs\Tab::make('Товары для набора')
                        ->schema([
                            Repeater::make('set_products')
                                ->label('Товары в наборе')
                                ->schema([
                                    Select::make('product_id')
                                        ->label('Товар(ы)')
                                        ->multiple()
                                        ->searchable()
                                        ->options(
                                            Product::pluck('name', 'id')
                                                ->toArray()
                                        )
                                        ->required()
                                ]),
                        ]),

                    Tabs\Tab::make('Настройки для сертификата')
                        ->schema([
                            Repeater::make('certificate_params')
                                ->label('Настройки для сертификата')
                                ->schema([
                                    Select::make('price')
                                        ->label('Цена')
                                        ->options([
                                            5000 => '5000р',
                                            10000 => '10000р',
                                            15000 => '15000р',
                                            20000 => '20000р',
                                        ])
                                        ->required(),

                                    FileUpload::make('image')
                                        ->label('Изображение сертификата')
                                        ->reorderable()
                                        ->required()
                                        ->directory('products'),

                                    Textarea::make('alt')
                                        ->label('Alt')
                                        ->required(),
                                ]),
                        ]),
                    Tabs\Tab::make('SEO')
                        ->schema([
                            TextInput::make('seo_title')
                                ->label('SEO Title')
                                ->required(),
                            Repeater::make('seo')
                                ->label('SEO Fields')
                                ->schema([
                                    TextInput::make('meta_tag')
                                        ->label('Meta Tag')
                                        ->default('description')
                                        ->hint('Например: description, keywords, robots, etc. '),

                                    TextInput::make('content')
                                        ->label('Content')
                                        ->hint('Содержимое meta tag')
                                ])
                        ]),
                ]),


            Placeholder::make('created_at')
                ->label('Created Date')
                ->content(fn(?Product $record): string => $record?->created_at?->diffForHumans() ?? '-'),

            Placeholder::make('updated_at')
                ->label('Last Modified Date')
                ->content(fn(?Product $record): string => $record?->updated_at?->diffForHumans() ?? '-'),
        ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('name')
                ->searchable()
                ->sortable(),

            TextColumn::make('name_en')
                ->searchable()
                ->sortable(),

            TextColumn::make('price'),

            TextColumn::make('description'),

            ImageColumn::make('image'),

            TextColumn::make('category_id'),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name'];
    }
}
