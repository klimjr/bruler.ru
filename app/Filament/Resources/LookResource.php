<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LookResource\Pages;
use App\Models\Look;
use App\Models\Product;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class LookResource extends Resource
{
    protected static ?string $model = Look::class;

    protected static ?string $navigationGroup = 'Товары';
    protected static ?int $navigationSort = 6;

    protected static ?string $slug = 'looks';

    protected static ?string $modelLabel = 'Образ';

    protected static ?string $pluralModelLabel = 'Образы';

//    protected static ?string $navigationIcon = 'heroicon-o-eye';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Название')
                    ->reactive()
                    ->debounce(1000)
                    ->afterStateUpdated(fn($state, callable $set) => $set('slug', \Str::slug($state)))
                    ->required(),

                TextInput::make('slug')
                    ->label('Ссылка')
                    ->required()
                    ->unique(ignoreRecord: true),

                FileUpload::make('image')
                    ->label('Превью')
                    ->required()
                    ->directory('looks'),

                FileUpload::make('image_inside')
                    ->label('Фото внутри')
                    ->required()
                    ->directory('looks'),

                Select::make('products')
                    ->multiple()
                    ->searchable()
                    ->label('Товары')
                    ->options(Product::all()->pluck('name', 'id')),

                Textarea::make('description')
                    ->label('Описание'),

                TextInput::make('position')
                    ->label('Позиция'),

                Toggle::make('active')
                    ->default(true)
                    ->label('Активность'),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->reorderable('position')
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
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
            'index' => Pages\ListLooks::route('/'),
            'create' => Pages\CreateLook::route('/create'),
            'edit' => Pages\EditLook::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return [];
    }
}
