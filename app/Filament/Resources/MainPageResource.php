<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MainPageResource\Pages;
use App\Filament\Resources\MainPageResource\RelationManagers;
use App\Models\MainPage;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use App\Models\Product;
use Filament\Forms\Components\DateTimePicker;

class MainPageResource extends Resource
{
    protected static ?string $model = MainPage::class;
    protected static ?string $navigationGroup = 'Настройки';

    protected static ?int $navigationSort = 777;

    protected static ?string $modelLabel = 'Главная';
    protected static ?string $pluralModelLabel = 'Настройки Главной Страницы';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                FileUpload::make('banner')
                    ->label('Баннер (ПК)'),
                FileUpload::make('banner_mobile')
                    ->label('Баннер (Мобильный)'),
                FileUpload::make('video')
                    ->label('Видео (ПК)'),
                FileUpload::make('video_mobile')
                    ->label('Видео (Мобильный)'),
                Forms\Components\TextInput::make('span_text')
                    ->label('Подзаголовок в Баннере')
                    ->maxLength(255),
                Forms\Components\TextInput::make('main_text')
                    ->label('Заголовок в Баннере')
                    ->maxLength(255),
                Forms\Components\TextInput::make('button_text')
                    ->label('Текст на кнопке в Баннере')
                    ->maxLength(255),
                Forms\Components\TextInput::make('button_link')
                    ->label('Ссылка для кнопки в Баннере')
                    ->maxLength(255),
                Forms\Components\TextInput::make('products_span_text')
                    ->label('Подзаголовок в товарах')
                    ->maxLength(255),
                Forms\Components\TextInput::make('products_main_text')
                    ->label('Заголовок в товарах')
                    ->maxLength(255),
                Forms\Components\Checkbox::make('one_plus_one')
                    ->label('Акция 1+1'),
                DateTimePicker::make('timer')
                    ->label('Таймер'),
                Forms\Components\Grid::make()
                    ->schema([
                        Select::make('products')
                            ->columnSpan(11)
                            ->multiple()
                            ->searchable()
                            ->label('Товары на главной')
                            ->options([
                                Product::all()->pluck('name_en', 'id')
                                    ->toArray()
                            ]),
                        Forms\Components\ViewField::make('goto-main')
                            ->view('filament.components.goto-main-button')
                            ->columnSpan(1),
                    ]),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('main_text')
                    ->label('Заголовок в Баннере')
                    ->searchable(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMainPages::route('/'),
            'create' => Pages\CreateMainPage::route('/create'),
            'view' => Pages\ViewMainPage::route('/{record}'),
            'edit' => Pages\EditMainPage::route('/{record}/edit'),
        ];
    }
}
