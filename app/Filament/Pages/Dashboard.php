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
    protected function getHeaderActions(): array
    {
        return [
            Action::make('generar_reporte')
                ->label('Reporte en Excel')
                ->icon('heroicon-o-document-arrow-down')
                ->color('success') // Indicador visual estándar para exportaciones de hojas de cálculo
                ->form([
                    DatePicker::make('fecha_inicio')
                        ->label('Fecha Inicial')
                        ->nullable(),
                        
                    DatePicker::make('fecha_fin')
                        ->label('Fecha Final')
                        ->nullable(),
                        
                    // Selección del responsable mediante consulta optimizada pluck para el listado
                    Select::make('responsable_id')
                        ->label('Funcionario / Responsable')
                        ->options(Responsable::pluck('nombre_apellido', 'idresponsables'))
                        ->searchable()
                        ->preload()
                        ->nullable(),
                ])
                ->action(function (array $data) {
                    // Instancia la clase exportadora inyectando el array de parámetros validados
                    // y retorna la respuesta de descarga binaria generada por Laravel-Excel
                    return Excel::download(
                        new ReporteBienesExport($data), 
                        'reporte_bienes_' . date('Ymd_His') . '.xlsx'
                    );
                })
                ->modalHeading('Generar Reporte de Bienes Asignados')
                ->modalSubmitActionLabel('Descargar Excel'),
        ];
    }
}