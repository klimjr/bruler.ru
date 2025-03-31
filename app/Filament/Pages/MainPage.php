<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class MainPage extends Page
{
    protected static ?string $navigationLabel = 'Главная';
    protected static ?string $title = 'Управление товарами на главной странице';
    protected static ?string $slug = 'main-page';

    protected static string $view = 'filament.pages.main-page';
    protected static string $layout = 'layouts.sortable';
    protected static ?string $navigationGroup = 'Настройки';
}
