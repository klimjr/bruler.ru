<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SetResource\Pages;
use App\Filament\Resources\SetResource\RelationManagers;
use App\Models\Product;
use App\Models\Set;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class SetResource extends Resource
{
    protected static ?string $model = Set::class;
    protected static ?string $navigationGroup = 'Товары';
    protected static ?int $navigationSort = 5;

    protected static ?string $modelLabel = 'Комплект';

    protected static ?string $pluralModelLabel = 'Комплекты';
//    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Название комплекта')
                    ->debounce(1000)
                    ->reactive()
                    ->afterStateUpdated(fn($state, callable $set) => $set('slug', Str::slug($state)))
                    ->required(),
                Forms\Components\TextInput::make('slug')
                    ->label('Значение для URL (slug)'),
                Select::make('products')
                    ->multiple()
                    ->options(Product::all()->pluck('name', 'id')),
                Forms\Components\Textarea::make('description')
                    ->label('Краткое описание'),
                Forms\Components\TextInput::make('position')
                    ->label('Позиция'),
                Forms\Components\Checkbox::make('active')
                    ->default(true)
                    ->label('Активный'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->reorderable('position')
            ->defaultSort('position', 'asc')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Называние комплекта'),

            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSets::route('/'),
            'create' => Pages\CreateSet::route('/create'),
            'edit' => Pages\EditSet::route('/{record}/edit'),
        ];
    }
}
