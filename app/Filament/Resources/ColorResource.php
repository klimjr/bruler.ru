<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ColorResource\Pages;
use App\Models\Color;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ColorResource extends Resource
{
    protected static ?string $modelLabel = 'Цвет';

    protected static ?string $navigationGroup = 'Cправочники';
    protected static ?int $navigationSort = 777;

    protected static ?string $pluralModelLabel = 'Цвета';
    protected static ?string $model = Color::class;

    protected static ?string $slug = 'colors';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('name')
                ->required(),

            TextInput::make('color')
                ->required(),

            TextInput::make('position')
                ->integer(),

            Placeholder::make('created_at')
                ->label('Created Date')
                ->content(fn(?Color $record): string => $record?->created_at?->diffForHumans() ?? '-'),

            Placeholder::make('updated_at')
                ->label('Last Modified Date')
                ->content(fn(?Color $record): string => $record?->updated_at?->diffForHumans() ?? '-'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('name')
                ->searchable()
                ->sortable(),

            TextColumn::make('color'),

            TextColumn::make('position'),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListColors::route('/'),
            'create' => Pages\CreateColor::route('/create'),
            'edit' => Pages\EditColor::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name'];
    }
}
