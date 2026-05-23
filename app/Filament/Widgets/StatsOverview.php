<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 2;
protected function getStats(): array
    {
        return [
            Stat::make('Total Activos', '29') // Reemplaza '29' con Bien::count() para que sea real
                ->description('Registrados en el sistema')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color('info'), // Azul claro

            Stat::make('Bienes Disponibles', '16') // Reemplaza con Bien::where('estado', 'DISPONIBLE')->count()
                ->description('Listos para asignación')
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('success'), // Verde vibrante

            Stat::make('Bienes Entregados', '13') // Reemplaza con Bien::where('estado', 'ENTREGADO')->count()
                ->description('En custodia de funcionarios')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('warning'), // Naranja vibrante
        ];
    }
        // Esta función oculta el widget si el usuario no es admin
    public static function canView(): bool
    {
         /** @var \App\Models\User|null $user */
        $user = Auth::user();

        return $user && $user->hasAnyRole(['admin', 'super_admin']);
    }

}
