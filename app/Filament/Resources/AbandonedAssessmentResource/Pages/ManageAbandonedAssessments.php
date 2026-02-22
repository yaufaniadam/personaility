<?php

namespace App\Filament\Resources\AbandonedAssessmentResource\Pages;

use App\Filament\Resources\AbandonedAssessmentResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageAbandonedAssessments extends ManageRecords
{
    protected static string $resource = AbandonedAssessmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
