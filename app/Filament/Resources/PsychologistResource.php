<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PsychologistResource\Pages;
use App\Models\Psychologist;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PsychologistResource extends Resource
{
    protected static ?string $model = Psychologist::class;
    protected static ?string $navigationIcon = 'heroicon-o-identification';
    protected static ?string $navigationGroup = 'Directory';
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Identity')->schema([
                Forms\Components\TextInput::make('name')->required(),
                Forms\Components\Select::make('gender')
                    ->options([
                        'male' => 'Laki-laki',
                        'female' => 'Perempuan',
                    ])
                    ->required(),
                Forms\Components\FileUpload::make('photo_path')
                    ->label('Photo')
                    ->image()
                    ->directory('psychologists/photos')
                    ->nullable()
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('str_number')->label('STR Number')->required(),
                Forms\Components\TextInput::make('sip_number')->label('SIP Number')->nullable(),
                Forms\Components\TextInput::make('specialization')->required(),
            ])->columns(2),

            Forms\Components\Section::make('Location')->schema([
                Forms\Components\Select::make('province_code')
                    ->label('Provinsi')
                    ->options(\Laravolt\Indonesia\Models\Province::pluck('name', 'code'))
                    ->searchable()
                    ->live()
                    ->afterStateUpdated(function ($state, Forms\Set $set) {
                        $set('city_code', null);
                        $set('province', \Laravolt\Indonesia\Models\Province::where('code', $state)->first()?->name);
                    })
                    ->required(),
                Forms\Components\Select::make('city_code')
                    ->label('Kota/Kabupaten')
                    ->options(fn (Forms\Get $get) => \Laravolt\Indonesia\Models\City::where('province_code', $get('province_code'))->pluck('name', 'code'))
                    ->searchable()
                    ->live()
                    ->afterStateUpdated(function ($state, Forms\Set $set) {
                        $set('city', \Laravolt\Indonesia\Models\City::where('code', $state)->first()?->name);
                    })
                    ->required(),
                Forms\Components\Hidden::make('province'),
                Forms\Components\Hidden::make('city'),
            ])->columns(2),

            Forms\Components\Section::make('Contact')->schema([
                Forms\Components\TextInput::make('contact_phone')->tel()->nullable(),
                Forms\Components\TextInput::make('contact_whatsapp')->tel()->nullable(),
                Forms\Components\TextInput::make('website')->url()->nullable(),
            ])->columns(3),

            Forms\Components\Section::make('Details')->schema([
                Forms\Components\Textarea::make('bio')->nullable()->columnSpanFull(),
                Forms\Components\Toggle::make('verified_status')->label('Verified (STR confirmed)'),
                Forms\Components\Toggle::make('active')->default(true),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable(),
                Tables\Columns\TextColumn::make('city')->searchable(),
                Tables\Columns\TextColumn::make('province')->searchable(),
                Tables\Columns\TextColumn::make('specialization'),
                Tables\Columns\IconColumn::make('verified_status')->boolean()->label('Verified'),
                Tables\Columns\IconColumn::make('active')->boolean(),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('verified_status'),
                Tables\Filters\TernaryFilter::make('active'),
            ])
            ->actions([Tables\Actions\EditAction::make()])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListPsychologists::route('/'),
            'create' => Pages\CreatePsychologist::route('/create'),
            'edit'   => Pages\EditPsychologist::route('/{record}/edit'),
        ];
    }
}
