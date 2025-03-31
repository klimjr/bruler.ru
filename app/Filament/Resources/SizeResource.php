<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SizeResource\Pages;
use App\Models\Size;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SizeResource extends Resource
{
    protected static ?string $modelLabel = 'Размер';

    protected static ?string $navigationGroup = 'Cправочники';

    protected static ?int $navigationSort = 777;

    protected static ?string $pluralModelLabel = 'Размеры';


    protected static ?string $model = Size::class;

    protected static ?string $slug = 'sizes';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('name')
                ->required(),

            TextInput::make('size')
                ->required(),

            TextInput::make('position')
                ->integer(),

            Placeholder::make('created_at')
                ->label('Created Date')
                ->content(fn(?Size $record): string => $record?->created_at?->diffForHumans() ?? '-'),

            Placeholder::make('updated_at')
                ->label('Last Modified Date')
                ->content(fn(?Size $record): string => $record?->updated_at?->diffForHumans() ?? '-'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('name')
                ->searchable()
                ->sortable(),

            TextColumn::make('size'),

            TextColumn::make('position'),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSizes::route('/'),
            'create' => Pages\CreateSize::route('/create'),
            'edit' => Pages\EditSize::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name'];
    }
}
