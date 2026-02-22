<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AbandonedAssessmentResource\Pages;
use App\Filament\Resources\AbandonedAssessmentResource\RelationManagers;
use App\Models\Assessment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AbandonedAssessmentResource extends Resource
{
    protected static ?string $model = Assessment::class;
    
    protected static ?string $slug = 'abandoned-assessments';
    
    protected static ?string $modelLabel = 'Abandoned Assessment';

    protected static ?string $navigationIcon = 'heroicon-o-x-circle';
    
    protected static ?string $navigationGroup = 'Asesmen';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Read-only info if needed
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('guest_name')
                    ->label('Nama Tamu')
                    ->searchable()
                    ->default('-'),
                Tables\Columns\TextColumn::make('guest_gender')
                    ->label('Gender')
                    ->sortable()
                    ->default('-'),
                Tables\Columns\TextColumn::make('guest_age_range')
                    ->label('Usia')
                    ->sortable()
                    ->default('-'),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('User Terdaftar')
                    ->searchable()
                    ->default('-'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat Pada')
                    ->dateTime()
                    ->sortable(),
                 Tables\Columns\TextColumn::make('updated_at')
                    ->label('Terakhir Aktif')
                    ->dateTime()
                    ->sortable()
                    ->badge()
                    ->color('warning'),
            ])
            ->defaultSort('updated_at', 'desc')
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereNull('completed_at');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageAbandonedAssessments::route('/'),
        ];
    }
}
