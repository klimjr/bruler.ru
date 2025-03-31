<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductVariantResource\Pages;
use App\Models\ProductVariant;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ProductVariantResource extends Resource
{

    protected static ?string $modelLabel = 'Товар - вариант';

    protected static ?string $navigationGroup = 'Товары';
    protected static ?int $navigationSort = 777;

    protected static ?string $pluralModelLabel = 'Товар - варианты';

    protected static ?string $model = ProductVariant::class;

    protected static ?string $slug = 'product-variants';

    protected static ?string $recordTitleAttribute = 'id';

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('product_id')
                ->required()
                ->integer(),

            TextInput::make('color_id')
                ->required()
                ->integer(),

            TextInput::make('size_id')
                ->required()
                ->integer(),

            TextInput::make('amount')
                ->required()
                ->integer(),

            TextInput::make('article')
                ->required()
                ->integer(),

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
                ->label('Изображение варианта')
                ->required()
                ->directory('product-variants'),

            Placeholder::make('created_at')
                ->label('Created Date')
                ->content(fn(?ProductVariant $record): string => $record?->created_at?->diffForHumans() ?? '-'),

            Placeholder::make('updated_at')
                ->label('Last Modified Date')
                ->content(fn(?ProductVariant $record): string => $record?->updated_at?->diffForHumans() ?? '-'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('product_id'),

            TextColumn::make('color_id'),

            TextColumn::make('size_id'),

            TextColumn::make('amount'),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProductVariants::route('/'),
            'create' => Pages\CreateProductVariant::route('/create'),
            'edit' => Pages\EditProductVariant::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return [];
    }
}
