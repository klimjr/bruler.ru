<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReadyMadeResource\Pages;
use App\Filament\Resources\ReadyMadeResource\RelationManagers;
use App\Models\ReadyMade;
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

class ReadyMadeResource extends Resource
{
    protected static ?string $model = ReadyMade::class;

    protected static ?int $navigationSort = 777;

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $modelLabel = 'Готовые образы';
    protected static ?string $pluralModelLabel = 'Готовые образы';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                FileUpload::make('preview')
                    ->label('Превью'),
                Select::make('products')
                    ->multiple()
                    ->searchable()
                    ->label('Товары')
                    ->options([
                        Product::all()->pluck('name_en', 'id')
                            ->toArray()
                    ])
            ]);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('preview')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListReadyMades::route('/'),
            'create' => Pages\CreateReadyMade::route('/create'),
            'view' => Pages\ViewReadyMade::route('/{record}'),
            'edit' => Pages\EditReadyMade::route('/{record}/edit'),
        ];
    }
}
