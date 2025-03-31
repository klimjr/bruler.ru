<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RunningTextResource\Pages;
use App\Models\Color;
use App\Models\RunningText;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class RunningTextResource extends Resource
{
    protected static ?string $modelLabel = 'Текст в шапке';

    protected static ?string $navigationGroup = 'Настройки';
    protected static ?int $navigationSort = 777;

    protected static ?string $pluralModelLabel = 'Текст в шапке';
    protected static ?string $model = RunningText::class;

    protected static ?string $slug = 'running_texts';

    protected static ?string $recordTitleAttribute = 'text';

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('text')
                ->label('Текст'),

            ColorPicker::make('text_color')
                ->label('Цвет текста')
                ->default('#ffffff'),

            ColorPicker::make('bg_color')
                ->label('Цвет заднего фона')
                ->default('#000000'),

            Placeholder::make('created_at')
                ->label('Дата создания')
                ->content(fn(?RunningText $record): string => $record?->created_at?->diffForHumans() ?? '-'),

            Placeholder::make('updated_at')
                ->label('Дата редактирования')
                ->content(fn(?RunningText $record): string => $record?->updated_at?->diffForHumans() ?? '-'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('id')
                ->sortable(),

            TextColumn::make('text')
                ->searchable(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRunningText::route('/'),
            'create' => Pages\CreateRunningText::route('/create'),
            'edit' => Pages\EditRunningText::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['text'];
    }
}
