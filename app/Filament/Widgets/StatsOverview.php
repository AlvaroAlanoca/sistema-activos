<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
\Filament\Widgets\StatsOverviewWidget\Stat::make('Total Activos', \App\Models\Bien::count())
            ->description('Bienes registrados')
            ->descriptionIcon('heroicon-m-cube')
            ->color('primary'),
            
        \Filament\Widgets\StatsOverviewWidget\Stat::make('Bienes Disponibles', \App\Models\Bien::where('estado', 'DISPONIBLE')->count())
            ->description('Listos para entrega')
            ->descriptionIcon('heroicon-m-check-badge')
            ->color('success'),
            
        \Filament\Widgets\StatsOverviewWidget\Stat::make('Bienes Entregados', \App\Models\Bien::where('estado', 'ENTREGADO')->count())
            ->description('En uso por funcionarios')
            ->descriptionIcon('heroicon-m-users')
            ->color('warning'),
        ];
    }
}
