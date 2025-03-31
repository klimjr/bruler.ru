<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AmbassadorResource\Pages;
use App\Filament\Resources\AmbassadorResource\RelationManagers;
use App\Models\Ambassador;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AmbassadorResource extends Resource
{
    protected static ?string $model = Ambassador::class;

    protected static ?int $navigationSort = 777;

    protected static ?string $modelLabel = 'Амбассадор';
    protected static ?string $pluralModelLabel = 'Амбассадоры';

    protected static ?string $navigationGroup = 'Маркетинг';
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Имя')
                    ->required()
                    ->maxLength(255),
                Forms\Components\FileUpload::make('image')
                    ->label('Изображение')
                    ->image(),
                Forms\Components\TextInput::make('position')
                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\ImageColumn::make('image'),
                Tables\Columns\TextColumn::make('position')
                    ->numeric()
                    ->sortable(),
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
            'index' => Pages\ListAmbassadors::route('/'),
            'create' => Pages\CreateAmbassador::route('/create'),
            'view' => Pages\ViewAmbassador::route('/{record}'),
            'edit' => Pages\EditAmbassador::route('/{record}/edit'),
        ];
    }
}
