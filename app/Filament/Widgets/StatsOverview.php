<?php

namespace App\Filament\Widgets;

use App\Models\Bien; // 👇 ESTA ES LA LÍNEA CLAVE QUE FALTABA
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 2;

    protected function getStats(): array
    {
        return [
            // Consulta real: Cuenta absolutamente todos los bienes en la tabla
            Stat::make('Total Activos', Bien::count()) 
                ->description('Registrados en el sistema')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color('info'), // Azul claro

            // Consulta real: Cuenta solo los que tienen estado 'DISPONIBLE'
            Stat::make('Bienes Disponibles', Bien::where('estado', 'DISPONIBLE')->count()) 
                ->description('Listos para asignación')
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('success'), // Verde vibrante

            // Consulta real: Cuenta solo los que tienen estado 'ENTREGADO'
            Stat::make('Bienes Entregados', Bien::where('estado', 'ENTREGADO')->count()) 
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