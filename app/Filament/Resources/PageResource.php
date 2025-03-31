<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PageResource\Pages;
use App\Models\Page;
use App\Models\Collection;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PageResource extends Resource
{
    protected static ?string $modelLabel = 'Страница';

    protected static ?int $navigationSort = 777;

    protected static ?string $pluralModelLabel = 'Страницы';

    protected static ?string $model = Page::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $slug = 'pages';

    protected static ?string $recordTitleAttribute = 'type';

    public static function form(Form $form): Form
    {

        $typeOptions = [
            Page::TYPE_MAIN_PAGE => 'Главная страница',
            Page::TYPE_ABOUT_BRAND => 'О бренде',
            Page::TYPE_COLLECTION => 'Магазин (коллекция)',
            Page::TYPE_DOCUMENTS => 'Документы',
            Page::TYPE_PREORDER => 'Предзаказ',
            Page::TYPE_REFUND => 'Возврат',
            Page::TYPE_PAYMENT => 'Оплата',
            Page::TYPE_DELIVERY => 'Доставка',
            Page::TYPE_CONTACTS => 'Контакты',
            Page::TYPE_PROFILE => 'Профиль',
            Page::TYPE_CART => 'Корзина',
            Page::TYPE_LOGIN => 'Вход в аккаунт',
            Page::TYPE_PROFILE_PASSWORD_RESET => 'Сброс пароля',
            Page::TYPE_PROFILE_ORDERS => 'История заказов',
            Page::TYPE_REGISTER => 'Регистрация',
            Page::TYPE_PROFILE_FAVOURITES => 'Избранные товары',
            Page::TYPE_OTHER => 'Страница не из списка',
        ];

        return $form->schema([
            TextInput::make('name')
                ->label('Название')
                ->required(),
            Select::make('type')
                ->label('Тип')
                ->options($typeOptions)
                ->searchable()
                ->required(),
            TextInput::make('route')
                ->label('Путь страницы')
                ->hint('Примеры пути - "/" (путь главной страницы), "about_brand" (путь страницы), "profile/favourites" (вложенный путь)')
                ->required(),
            TextInput::make('h1')
                ->label('Заголовок страницы')
                ->hint('h1 страницы')
                ->required(),
            Tabs::make('Label')
                ->tabs([
                    Tabs\Tab::make('SEO')
                        ->schema([
                            TextInput::make('seo_title')
                                ->label('SEO Title'),
                            Repeater::make('seo_fields')
                                ->label('SEO Fields')
                                ->schema([
                                    TextInput::make('meta_tag')
                                        ->label('Meta Tag')
                                        ->default('description')
                                        ->hint('Например: description, keywords, robots, etc. '),

                                    TextInput::make('content')
                                        ->label('Content')
                                        ->hint('Содержимое meta tag')
                                ])
                                ->columns(1),

                        ])
                ]),
        ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('name')
                ->label('Название'),
            TextColumn::make('type')
                ->label('Тип'),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPages::route('/'),
            'create' => Pages\CreatePage::route('/create'),
            'edit' => Pages\EditPage::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return [];
    }
}
