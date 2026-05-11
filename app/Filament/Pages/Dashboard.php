<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use App\Models\Responsable;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ReporteBienesExport;

class Dashboard extends BaseDashboard
{
    protected static ?string $title = 'Panel de Control';

    protected static ?string $navigationLabel = 'Panel de Control';

    protected static ?string $navigationIcon = 'heroicon-o-home';

    /**
     * Define las acciones disponibles en la cabecera de la página.
     * Implementa la acción para solicitar parámetros y ejecutar la exportación del reporte de bienes en formato Excel.
     *
     * @return array
     */

    
}
