<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ActionResource\Pages;
use App\Models\Action;
use App\Models\Product;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;

class ActionResource extends Resource
{
    protected static ?string $model = Action::class;
    protected static ?string $navigationGroup = 'Маркетинг';

    protected static ?string $slug = 'actions';

    protected static ?string $recordTitleAttribute = 'Акция';

    protected static ?string $pluralLabel = 'Акции';

//    protected static ?string $navigationIcon = 'heroicon-o-percent-badge';

    public static function form(Form $form): Form
    {
        $products = Product::query()->pluck('name', 'id');
        return $form
            ->schema([
                TextInput::make('name')
                    ->columnSpan(2)
                    ->label('Название акции')
                    ->required(),
                Textarea::make('description')
                    ->columnSpan(2)
                    ->label('Описание акции'),

                Section::make('Бейдж')
                    ->columns(2)
                    ->description('Укажите бейдж для акции')
                    ->collapsed()
                    ->compact()
                    ->schema([
                        TextInput::make('badge')->label('Бейдж'),

                        ColorPicker::make('badge_color')->label('Цвет'),
                    ]),

                Section::make('Продукты')
                    ->description(new HtmlString('Выберите продукты для акции.</br>
                    <span class="text-danger-600">ВНИМАНИЕ!</span><br>
                     Товары, включенные, исключенные и связанные с акцией, это одинаковые списки и не исключаются друг от друга!<br>
                     Т.е. во всех списках видны одинаковые товары.
                     ') )
                    ->compact()
                    ->columns(3)
                    ->schema([
                        Checkbox::make('all_products')
                            ->columnSpan(3)
                            ->label('Применить на все товары'),
                        Select::make('products_include_ids')
                            ->label('Товары включенные в акцию')
                            ->options($products)
                            ->searchable()
                            ->multiple(),
                        Select::make('products_exclude_ids')
                            ->options($products)
                            ->label('Товары исключенные из акции')
                            ->searchable()
                            ->multiple(),
                        Select::make('products_related_ids')
                            ->options($products)
                            ->label('Товары связанные с акцией')
                            ->searchable()
                            ->multiple(),

                    ]),

                Section::make('Скидка')
                    ->description('Задайте скидку на акцию.
                     Скидки применяются к цене товара.
                     Только одна скидка может применяться к товару.
                     Скидка может быть процентная или фиксированная.
                     ')
                    ->compact()
                    ->columns(2)
                    ->schema([
                        TextInput::make('discount_amount')
                            ->label('Скидка или цена')
                            ->numeric(),
                        Select::make('discount_type')
                            ->options([
                                'percent' => 'Процент',
                                'fixed' => 'Фиксированная сумма',
                                'free' => 'Бесплатно',
                            ])
                            ->label('Тип скидки'),
                    ]),


                Checkbox::make('is_active')->label('Активна ли акция?'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Название акции')
                    ->searchable()
                    ->sortable(),

                ToggleColumn::make('is_active')
                    ->label('Активна'),
            ])
            ->filters([
                //
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListActions::route('/'),
            'create' => Pages\CreateAction::route('/create'),
            'edit' => Pages\EditAction::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name'];
    }
}
