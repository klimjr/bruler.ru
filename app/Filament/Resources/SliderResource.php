<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SliderResource\Pages;
use App\Models\Slider;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SliderResource extends Resource
{
    protected static ?string $modelLabel = 'Изображение для слайдера';
    protected static ?string $navigationGroup = 'Настройки';
    protected static ?int $navigationSort = 777;

    protected static ?string $pluralModelLabel = 'Слайдер на главной';
    protected static ?string $model = Slider::class;

    protected static ?string $slug = 'slider';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('name')
                ->label('Имя')
                ->required(),

            FileUpload::make('image')
                ->label('Изображение для слайдера')
                ->required()
                ->directory('slider'),

            TextInput::make('position')
                ->label('Позиция')
                ->integer(),

            Placeholder::make('created_at')
                ->label('Created Date')
                ->content(fn(?Slider $record): string => $record?->created_at?->diffForHumans() ?? '-'),

            Placeholder::make('updated_at')
                ->label('Last Modified Date')
                ->content(fn(?Slider $record): string => $record?->updated_at?->diffForHumans() ?? '-'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('name')
                ->searchable()
                ->sortable(),

            ImageColumn::make('image'),

            TextColumn::make('position'),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSlider::route('/'),
            'create' => Pages\CreateSlider::route('/create'),
            'edit' => Pages\EditSlider::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name'];
    }
}
