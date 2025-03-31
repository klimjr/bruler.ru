<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\OrdersRelationshipManager;
use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ViewColumn;
use Filament\Tables\Table;

class UserResource extends Resource
{
    protected static ?string $pluralModelLabel = 'Пользователи';
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Имя')
                    ->readOnly(),

                TextInput::make('last_name')
                    ->label('Фамилия')
                    ->readOnly(),

                TextInput::make('email')
                    ->label('Почта')
                    ->readOnly(),

                TextInput::make('phone')
                    ->label('Телефон')
                    ->readOnly(),

                TextInput::make('city')
                    ->label('Город'),

                TextInput::make('birthday')
                    ->label('Дата рождения'),

                TextInput::make('telegram_id')
                    ->label('Telegram ID')
                    ->readOnly(),

                TextInput::make('vk_id')
                    ->label('VK ID')
                    ->readOnly(),

                TextInput::make('points')
                    ->label('Баллы')
                    ->numeric(),

                FileUpload::make('image')
                    ->label('Аватар'),

                Placeholder::make('email_verified_at')
                    ->label('Дата подтверждения почты')
                    ->content(fn(?User $record): string => $record?->email_verified_at?->diffForHumans() ?? '-'),

                Placeholder::make('created_at')
                    ->label('Дата регистрации')
                    ->content(fn(?User $record): string => $record?->created_at?->diffForHumans() ?? '-'),

                Placeholder::make('updated_at')
                    ->label('Дата обновления информации')
                    ->content(fn(?User $record): string => $record?->updated_at?->diffForHumans() ?? '-'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('#'),
                Tables\Columns\TextColumn::make('name')->label('Имя'),
                Tables\Columns\TextColumn::make('last_name')->label('Фамилия'),
                Tables\Columns\TextColumn::make('email')->label('E-mail'),
                Tables\Columns\TextColumn::make('phone')->label('Телефон'),
                Tables\Columns\TextColumn::make('city')->label('Город')->placeholder('-'),
                Tables\Columns\TextColumn::make('birthday')
                    ->date('d.m.Y')
                    ->placeholder('-')
                    ->label('Дата рождения'),
                ViewColumn::make('orders')
                    ->label('Кол-во заказов')
                    ->view('filament.table.filter-link'),
                //                TextColumn::make('orders_count')
//                    ->label('Кол-во заказов')
//                    ->placeholder('-')
//                    ->counts('orders'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])->defaultSort('id', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            'orders' => OrdersRelationshipManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
