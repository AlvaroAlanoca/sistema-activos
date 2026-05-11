<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ReporteBienesExport;
use Barryvdh\DomPDF\Facade\Pdf;

class ReporteGeneral extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-document-chart-bar';
    protected static ?string $navigationLabel = 'Reporte General';
    protected static ?string $title = 'Reporte General de Bienes';
    protected static string $view = 'filament.pages.reporte-general';
        protected static ?string $navigationGroup = 'Reportes';
    protected static ?int $navigationSort = 1;

    public ?array $data = [];

    public function mount(): void
    {
        // Inicializamos el formulario con las fechas del mes actual por defecto (opcional)
        $this->form->fill([
            'fecha_inicio' => now()->startOfMonth()->format('Y-m-d'),
            'fecha_fin' => now()->format('Y-m-d'),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Filtros por Rango de Fechas')
                    ->description('Especifique el periodo de tiempo para consolidar la información de los activos.')
                    ->schema([
                        Grid::make(2)->schema([
                            DatePicker::make('fecha_inicio')
                                ->label('Fecha Inicial')
                                ->required(),
                                
                            DatePicker::make('fecha_fin')
                                ->label('Fecha Final')
                                ->required(),
                        ]),
                    ])
            ])
            ->statePath('data');
    }

    public function descargarExcel()
    {
        $filtros = $this->form->getState();
        return Excel::download(
            new ReporteBienesExport($filtros), 
            'reporte_general_' . date('Ymd') . '.xlsx'
        );
    }

    public function descargarPdf()
    {
        $filtros = $this->form->getState();
        $reporte = new ReporteBienesExport($filtros);
        $items = $reporte->collection();
        
        // Reutilizamos la vista profesional que creamos anteriormente
        $pdf = Pdf::loadView('pdf.reporte-consolidado', [
            'items' => $items, 
            'filtros' => $filtros
        ])->setPaper('letter', 'landscape'); // Formato horizontal para mejor visibilidad
        
        return response()->streamDownload(
            fn () => print($pdf->output()), 
            "reporte_general_" . date('Ymd') . ".pdf"
        );
    }
}