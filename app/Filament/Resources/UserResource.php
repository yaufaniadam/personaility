<?php

namespace App\Filament\Resources;

use App\Enums\UserRole;
use App\Enums\SubscriptionStatus;
use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'User Management';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')->required(),
            Forms\Components\TextInput::make('email')->email()->required()->unique(ignoreRecord: true),
            Forms\Components\Select::make('role')
                ->options(collect(UserRole::cases())->mapWithKeys(fn ($e) => [$e->value => ucfirst($e->value)]))
                ->required(),
            Forms\Components\Select::make('subscription_status')
                ->options(collect(SubscriptionStatus::cases())->mapWithKeys(fn ($e) => [$e->value => ucfirst($e->value)]))
                ->required(),
            Forms\Components\DateTimePicker::make('subscription_expires_at')->nullable(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable(),
                Tables\Columns\TextColumn::make('email')->searchable(),
                Tables\Columns\BadgeColumn::make('role')->colors(['primary' => 'admin', 'gray' => 'user']),
                Tables\Columns\BadgeColumn::make('subscription_status')->colors(['success' => 'premium', 'gray' => 'free']),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('role')->options(['admin' => 'Admin', 'user' => 'User']),
                Tables\Filters\SelectFilter::make('subscription_status')->options(['free' => 'Free', 'premium' => 'Premium']),
            ])
            ->actions([Tables\Actions\EditAction::make()])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit'   => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
