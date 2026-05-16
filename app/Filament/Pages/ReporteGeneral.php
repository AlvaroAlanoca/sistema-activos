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
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
class ReporteGeneral extends Page implements HasForms
{
    use InteractsWithForms;
    use HasPageShield;
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
        
        // Capturamos quién está descargando el Excel
        $usuarioActivo = \Illuminate\Support\Facades\Auth::user();
        $generadoPor = ($usuarioActivo && $usuarioActivo->responsable) 
            ? $usuarioActivo->responsable->nombre_apellido 
            : ($usuarioActivo ? $usuarioActivo->name : 'Sistema');

        // Le enviamos la variable $generadoPor a la clase del Excel
        return Excel::download(
            new ReporteBienesExport($filtros, $generadoPor), 
            'reporte_bienes_' . date('Ymd_Hi') . '.xlsx'
        );
    }

public function descargarPdf()
    {
        $filtros = $this->form->getState();
        $reporte = new ReporteBienesExport($filtros);
        $items = $reporte->collection();
        
        // 1. CAPTURAMOS AL USUARIO EN SESIÓN
        $usuarioActivo = \Illuminate\Support\Facades\Auth::user();
        
        // 2. EXTRAEMOS SU NOMBRE (Con validación por si es un admin sin ficha)
        $generadoPor = ($usuarioActivo && $usuarioActivo->responsable) 
            ? $usuarioActivo->responsable->nombre_apellido 
            : ($usuarioActivo ? $usuarioActivo->name : 'Sistema');
        
        // 3. PASAMOS LA VARIABLE A LA VISTA
        $pdf = Pdf::loadView('pdf.reporte-consolidado', [
            'items' => $items, 
            'filtros' => $filtros,
            'generado_por' => $generadoPor // <-- Aquí inyectamos el nombre
        ])->setPaper('letter', 'landscape');
        
        return response()->streamDownload(
            fn () => print($pdf->output()), 
            "reporte_general_" . date('Ymd') . ".pdf"
        );
    }
}