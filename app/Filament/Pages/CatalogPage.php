<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class CatalogPage extends Page
{
    protected static ?string $navigationLabel = 'Каталог';
    protected static ?string $title = 'Управление каталогом';
    protected static ?string $slug = 'catalog';

    protected static string $view = 'filament.pages.catalog-page';
    protected static string $layout = 'layouts.sortable';
    protected static ?string $navigationGroup = 'Настройки';
}
