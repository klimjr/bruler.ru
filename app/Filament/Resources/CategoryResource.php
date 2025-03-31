<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryResource\Pages;
use App\Models\Category;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class CategoryResource extends Resource
{
    protected static ?string $modelLabel = 'Категория';

    protected static ?string $navigationGroup = 'Товары';
    protected static ?int $navigationSort = 777;

    protected static ?string $pluralModelLabel = 'Категории';

    protected static ?string $model = Category::class;

    protected static ?string $slug = 'categories';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('name')
                ->required()
                ->reactive()
                ->afterStateUpdated(fn($state, callable $set) => $set('slug', Str::slug($state))),

            TextInput::make('slug')
                ->required()
                ->unique(Category::class, 'slug', fn($record) => $record),

            TextInput::make('discount')
                ->minValue(0)
                ->maxValue(100)
                ->label('Скидка на категорию')
                ->numeric(),

            FileUpload::make('image')
                ->directory('categories')
                ->required(),

            Tabs::make('Label')
                ->tabs([
                    Tabs\Tab::make('SEO')
                        ->schema([
                            TextInput::make('seo_title')
                                ->label('SEO Title'),
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
                                ->columns(1),

                        ]),
                ]),
            TextInput::make('order')
                ->label('Порядковый номер')
                ->numeric(),
        ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('order', 'asc')
            ->reorderable('order')
            ->columns([
            TextColumn::make('name')
                ->searchable()
                ->sortable(),

            ImageColumn::make('image'),

            TextColumn::make('slug')
                ->searchable()
                ->sortable(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'edit' => Pages\EditCategory::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'slug'];
    }
}
