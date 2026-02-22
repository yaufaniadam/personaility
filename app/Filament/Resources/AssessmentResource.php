<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AssessmentResource\Pages;
use App\Models\Assessment;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AssessmentResource extends Resource
{
    protected static ?string $model = Assessment::class;
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationGroup = 'Assessment';
    protected static ?int $navigationSort = 3;

    // Read-only – no create/edit
    public static function canCreate(): bool { return false; }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Infolists\Components\Section::make('User Data')->schema([
                Infolists\Components\TextEntry::make('user.name')->label('User Terdaftar')->default('-'),
                Infolists\Components\TextEntry::make('guest_name')->label('Nama Tamu')->default('-'),
                Infolists\Components\TextEntry::make('guest_gender')->label('Gender')->default('-'),
                Infolists\Components\TextEntry::make('guest_age_range')->label('Usia')->default('-'),
                Infolists\Components\TextEntry::make('completed_at')->dateTime(),
                Infolists\Components\TextEntry::make('version'),
            ])->columns(3),

            Infolists\Components\Section::make('Trait Scores')->schema([
                Infolists\Components\TextEntry::make('openness_score')->label('Openness'),
                Infolists\Components\TextEntry::make('conscientiousness_score')->label('Conscientiousness'),
                Infolists\Components\TextEntry::make('extraversion_score')->label('Extraversion'),
                Infolists\Components\TextEntry::make('agreeableness_score')->label('Agreeableness'),
                Infolists\Components\TextEntry::make('neuroticism_score')->label('Neuroticism'),
            ])->columns(5),

            Infolists\Components\Section::make('AI Insight')->schema([
                Infolists\Components\TextEntry::make('aiInsight.core_strength')->label('Core Strength')->columnSpanFull(),
                Infolists\Components\TextEntry::make('aiInsight.blind_spot')->label('Blind Spot')->columnSpanFull(),
                Infolists\Components\TextEntry::make('aiInsight.growth_suggestion')->label('Growth Suggestion')->columnSpanFull(),
                Infolists\Components\TextEntry::make('aiInsight.stress_pattern')->label('Stress Pattern')->columnSpanFull(),
                Infolists\Components\TextEntry::make('aiInsight.model_version')->label('AI Model'),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')->searchable()->label('User Terdaftar')->default('-'),
                Tables\Columns\TextColumn::make('guest_name')->searchable()->label('Nama Tamu')->default('-'),
                Tables\Columns\TextColumn::make('guest_gender')->sortable()->label('Gender')->default('-'),
                Tables\Columns\TextColumn::make('guest_age_range')->sortable()->label('Usia')->default('-'),
                Tables\Columns\TextColumn::make('openness_score')->label('O')->numeric(decimalPlaces: 2),
                Tables\Columns\TextColumn::make('conscientiousness_score')->label('C')->numeric(decimalPlaces: 2),
                Tables\Columns\TextColumn::make('extraversion_score')->label('E')->numeric(decimalPlaces: 2),
                Tables\Columns\TextColumn::make('agreeableness_score')->label('A')->numeric(decimalPlaces: 2),
                Tables\Columns\TextColumn::make('neuroticism_score')->label('N')->numeric(decimalPlaces: 2),
                Tables\Columns\TextColumn::make('version'),
                Tables\Columns\TextColumn::make('completed_at')->dateTime()->sortable(),
            ])
            ->filters([])
            ->actions([Tables\Actions\ViewAction::make()])
            ->defaultSort('completed_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAssessments::route('/'),
            'view'  => Pages\ViewAssessment::route('/{record}'),
        ];
    }
}
