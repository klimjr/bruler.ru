<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CertificateResource\Pages;
use App\Http\Controllers\DolyamiController;
use App\Models\Category;
use App\Models\Certificate;
use App\Models\Color;
use App\Models\Order;
use App\Models\RunningText;
use App\Models\User;
use App\Notifications\CertificateNotificataion;
use Carbon\Carbon;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;

class CertificateResource extends Resource
{
    protected static ?string $modelLabel = 'Купленные сертификаты';

    protected static ?string $navigationGroup = 'Маркетинг';

    protected static ?int $navigationSort = 777;

    protected static ?string $pluralModelLabel = 'Купленные сертификаты';
    protected static ?string $model = Certificate::class;

    protected static ?string $slug = 'certificates';

    protected static ?string $recordTitleAttribute = 'id';

    public static function form(Form $form): Form
    {
        return $form->schema([

            Select::make('order_id')
                ->label('Номер заказа')
                ->live()
                ->options(
                    Order::all()->pluck('id', 'id')
                        ->toArray()
                ),

            TextInput::make('target_email')
                ->label('Указанная почта')
                ->email(),

            TextInput::make('remains')
                ->label('Остаточная сумма')
                ->numeric(),

            TextInput::make('code')
                ->label('Код для использования'),

            Placeholder::make('used_at')
                ->label('Дата последнего использования')
                ->content(fn(?Certificate $record): string => Carbon::parse($record?->used_at)?->diffForHumans() ?? '-'),

            DateTimePicker::make('expires_at')
                ->label('Истекает'),

            Placeholder::make('created_at')
                ->label('Дата создания')
                ->content(fn(?Certificate $record): string => $record?->created_at?->diffForHumans() ?? '-'),

            Placeholder::make('updated_at')
                ->label('Дата редактирования')
                ->content(fn(?Certificate $record): string => $record?->updated_at?->diffForHumans() ?? '-'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('id')
                ->label('#')
                ->sortable(),

            TextColumn::make('order_id')
                ->label('Номер заказа'),

            TextColumn::make('target_email')
                ->label('Указанная почта'),

            TextColumn::make('remains')
                ->label('Остаточная сумма'),

            TextColumn::make('code')
                ->label('Код для использования'),
        ])
            ->bulkActions([
                BulkActionGroup::make([
                    BulkAction::make('Повторно-отправить-письмо')
                        ->requiresConfirmation()
                        ->action(function (Collection $records) {
                            $records->each(function ($certificate) {
                                $notificationUser = new User();
                                $notificationUser->email = $certificate->target_email;
                                $notificationUser->notify(new CertificateNotificataion($certificate->code, isset($certificate->order['certificate']) ? $certificate->order['certificate']['certificate']['image'] : 'https://bruler.ru/storage/products/01HPF8R10B37P4A8T36TZRCK9R.jpg'));
                            });
                        }),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCertificate::route('/'),
            'create' => Pages\CreateCertificate::route('/create'),
            'edit' => Pages\EditCertificate::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['code'];
    }
}
