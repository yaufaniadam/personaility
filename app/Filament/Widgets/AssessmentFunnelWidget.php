<?php

namespace App\Filament\Widgets;

use App\Models\Assessment;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AssessmentFunnelWidget extends BaseWidget
{
    protected static ?string $pollingInterval = '30s';
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $totalStarted = Assessment::count();
        $totalCompleted = Assessment::whereNotNull('completed_at')->count();
        
        $dropOffCount = $totalStarted - $totalCompleted;
        $dropOffRate = $totalStarted > 0 ? round(($dropOffCount / $totalStarted) * 100, 1) : 0;
        
        $completionRate = $totalStarted > 0 ? round(($totalCompleted / $totalStarted) * 100, 1) : 0;

        return [
            Stat::make('Asesmen Dimulai', $totalStarted)
                ->description('Total sesi yang terbuat')
                ->descriptionIcon('heroicon-m-play')
                ->color('info'),
                
            Stat::make('Asesmen Selesai', $totalCompleted)
                ->description("{$completionRate}% Completion Rate")
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('success'),
                
            Stat::make('Drop-off (Tidak Selesai)', $dropOffCount)
                ->description("{$dropOffRate}% Drop-off Rate")
                ->descriptionIcon('heroicon-m-arrow-trending-down')
                ->color($dropOffRate > 40 ? 'danger' : 'warning'),
        ];
    }
}
