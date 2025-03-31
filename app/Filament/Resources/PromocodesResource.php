<?php

namespace App\Filament\Resources;


use App\Filament\Resources\PromocodesResource\CreatePromocodes;
use App\Filament\Resources\PromocodesResource\EditPromocodes;
use App\Filament\Resources\PromocodesResource\ListPromocodes;
use App\Models\Promocode;
use App\Models\Product;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Table;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Select;
use Filament\Forms\Get;

class PromocodesResource extends Resource
{

  protected static ?string $modelLabel = 'Промокоды';

    protected static ?int $navigationSort = 777;

  protected static ?string $pluralModelLabel = 'Промокоды';

    protected static ?string $navigationGroup = 'Маркетинг';

  protected static ?string $model = Promocode::class;

  protected static ?string $slug = 'promocodes';

  protected static ?string $recordTitleAttribute = 'id';

  public static function form(Form $form): Form
  {
    return $form->schema([
      Toggle::make('active')
        ->label('Активность'),

      TextInput::make('code')
        ->required()
        ->string(),

      TextInput::make('discount')
        ->required()
        ->integer(),

      TextInput::make('quantity')
        ->required()
        ->integer(),

      Toggle::make('applies_to_all_products')
        ->label('Применять ко всем товарам')
        ->default(true)
        ->reactive(),

      Select::make('applicable_products')
        ->multiple()
        ->options(Product::query()->pluck('name', 'id'))
        ->searchable()
        ->preload()
        ->label('Выберите товары')
        ->hidden(fn (Get $get): bool => $get('applies_to_all_products')),
    ]);
  }

  public static function table(Table $table): Table
  {
    return $table->columns([
      TextColumn::make('code'),
      TextColumn::make('discount'),
      TextColumn::make('quantity'),
      IconColumn::make('applies_to_all_products')
        ->label('Для всех товаров')
        ->boolean(),
      TextColumn::make('applicable_products')
        ->label('Кол-во выбранных товаров')
        ->formatStateUsing(function ($state) {
            if (empty($state)) return 0;

            if (is_string($state)) {
                $products = array_filter(explode(',', $state));
                return count($products);
            }
            return is_array($state) ? count($state) : 0;
        }),
    ]);
  }

  public static function getPages(): array
  {
    return [
      'index' => ListPromocodes::route('/'),
      'create' => CreatePromocodes::route('/create'),
      'edit' => EditPromocodes::route('/{record}/edit'),
    ];
  }

  public static function getGloballySearchableAttributes(): array
  {
    return [];
  }

  public static function activateAllPromocodes()
  {
    Promocode::query()->update(['active' => 1]);
  }

  public static function deactivateAllPromocodes()
  {
    Promocode::query()->update(['active' => 0]);
  }
}
