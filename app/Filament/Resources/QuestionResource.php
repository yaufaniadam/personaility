<?php

namespace App\Filament\Resources;

use App\Enums\PersonalityTrait;
use App\Filament\Resources\QuestionResource\Pages;
use App\Models\Question;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class QuestionResource extends Resource
{
    protected static ?string $model = Question::class;
    protected static ?string $navigationIcon = 'heroicon-o-question-mark-circle';
    protected static ?string $navigationGroup = 'Assessment';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('trait')
                ->options(collect(PersonalityTrait::cases())->mapWithKeys(fn ($e) => [$e->value => ucfirst($e->value)]))
                ->required(),
            Forms\Components\Textarea::make('question_text')->required()->columnSpanFull(),
            Forms\Components\Toggle::make('is_reverse')->label('Reverse scored')->helperText('Score = 6 − Likert value'),
            Forms\Components\Toggle::make('allow_note')->label('Allow reflective note'),
            Forms\Components\TextInput::make('order_number')->numeric()->default(0),
            Forms\Components\Toggle::make('active')->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order_number')->sortable()->label('#'),
                Tables\Columns\BadgeColumn::make('trait'),
                Tables\Columns\TextColumn::make('question_text')->limit(60)->searchable(),
                Tables\Columns\IconColumn::make('is_reverse')->boolean()->label('Reverse'),
                Tables\Columns\IconColumn::make('allow_note')->boolean()->label('Note'),
                Tables\Columns\IconColumn::make('active')->boolean(),
            ])
            ->defaultSort('order_number')
            ->filters([
                Tables\Filters\SelectFilter::make('trait')
                    ->options(collect(PersonalityTrait::cases())->mapWithKeys(fn ($e) => [$e->value => ucfirst($e->value)])),
                Tables\Filters\TernaryFilter::make('active'),
            ])
            ->actions([Tables\Actions\EditAction::make()])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListQuestions::route('/'),
            'create' => Pages\CreateQuestion::route('/create'),
            'edit'   => Pages\EditQuestion::route('/{record}/edit'),
        ];
    }
}
