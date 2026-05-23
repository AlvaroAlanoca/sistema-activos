<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
// Importamos los modelos para que los datos sean reales
use App\Models\Bien;

class EstadoBienesChart extends ChartWidget
{
    protected static ?string $heading = 'Estado Patrimonial de Activos';
    
    // Lo mantenemos en su propia fila
    protected int | string | array $columnSpan = 'full'; 
    protected static ?int $sort = 3;

    protected function getData(): array
    {
        return [
            'datasets' => [
                [
                    'label' => 'Activos',
                    // Consultas reales a tu base de datos
                    'data' => [
                        Bien::where('estado', 'DISPONIBLE')->count(),
                        Bien::where('estado', 'ENTREGADO')->count(),
                        Bien::where('estado', 'DE BAJA')->count(),
                    ],
                    // Paleta vibrante que combina con tus botones
                    'backgroundColor' => ['#06b6d4', '#f59e0b', '#ef4444'],
                    'hoverOffset' => 20, // Efecto visual al pasar el ratón
                    'borderWidth' => 0,   // Sin bordes para un look más limpio
                ],
            ],
            'labels' => ['Disponibles', 'En Custodia', 'Dados de Baja'],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

protected function getOptions(): array
{
    return [
        'cutout' => '75%',
        // ESTA ES LA CLAVE: Apagar las líneas de fondo
        'scales' => [
            'x' => ['display' => false],
            'y' => ['display' => false],
        ],
        'plugins' => [
            'legend' => [
                'display' => true,
                'position' => 'bottom',
                'labels' => ['usePointStyle' => true, 'padding' => 20],
            ],
        ],
    ];
}
}