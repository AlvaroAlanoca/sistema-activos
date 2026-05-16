<?php
namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Filament\Forms\Get;
use App\Models\Responsable;
use App\Models\Rubro;
use App\Models\TipoBien;
use App\Models\Bien;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ReporteBienesExport;
use Barryvdh\DomPDF\Facade\Pdf;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;

// Es obligatorio implementar HasForms para tener el formulario en la vista principal
class BusquedaEspecifica extends Page implements HasForms
{
    use InteractsWithForms;
    use HasPageShield;

    protected static ?string $navigationIcon = 'heroicon-o-magnifying-glass-circle';
    protected static ?string $navigationLabel = 'Búsqueda Específica';

    protected static string $view = 'filament.pages.busqueda-especifica';
    protected static ?string $navigationGroup = 'Reportes';
    protected static ?int $navigationSort = 2;
    

    // Aquí se guardará lo que el usuario escriba en el formulario
    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    // Definimos el formulario incrustado en lugar del Action
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Filtros de Búsqueda')
                    ->description('Seleccione los parámetros para generar su reporte.')
                    ->schema([
                        Grid::make(2)->schema([
                            DatePicker::make('fecha_inicio')->label('Fecha Inicial'),
                            DatePicker::make('fecha_fin')->label('Fecha Final'),
                        ]),
                        
                        Select::make('responsable_id')
                            ->label('Responsable')
                            ->options(Responsable::pluck('nombre_apellido', 'idresponsables'))
                            ->searchable(),

                        Grid::make(2)->schema([
                           // ... dentro de la función form()

Select::make('rubro_id')
    ->label('Seleccionar Rubro')
    ->options(Rubro::pluck('descripcion', 'idrubros'))
    ->live()
    ->afterStateUpdated(function (Select $component) {
        // Al cambiar el rubro, reseteamos los dos hijos
        $component->getContainer()->getComponent('tipo_bien_select')->state(null);
        $component->getContainer()->getComponent('bien_select')->state(null);
    }),

Select::make('tipo_bien_id')
    ->label('Tipo de Bien')
    ->key('tipo_bien_select')
    ->options(fn (Get $get) => 
        TipoBien::where('id_rubro', $get('rubro_id'))->pluck('descripcion', 'idtipo_bien')
    )
    ->live() // Ahora este también es live para activar el siguiente
    ->afterStateUpdated(fn (Select $component) => $component->getContainer()->getComponent('bien_select')->state(null))
    ->placeholder(fn (Get $get) => $get('rubro_id') ? 'Seleccione un tipo...' : 'Primero seleccione un rubro')
    ->disabled(fn (Get $get) => ! $get('rubro_id'))
    ->searchable(),

Select::make('bien_id')
    ->label('Bien Específico')
    ->key('bien_select')
    ->options(fn (Get $get) => 
        // Buscamos los bienes que pertenecen al tipo seleccionado
        Bien::where('id_tipo_bien', $get('tipo_bien_id'))->pluck('descripcion', 'idbienes')
    )
    ->placeholder(fn (Get $get) => $get('tipo_bien_id') ? 'Seleccione un bien...' : 'Primero seleccione un tipo de bien')
    ->disabled(fn (Get $get) => ! $get('tipo_bien_id'))
    ->searchable(),
                        ]),
                    ])
            ])
            ->statePath('data'); // Conecta los campos con la variable $data
    }

    // Método para descargar el Excel (Ejecutado desde la vista)
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
// Método para descargar el PDF (Ejecutado desde la vista)
    public function descargarPdf()
    {
        $filtros = $this->form->getState();
        $reporte = new ReporteBienesExport($filtros);
        $items = $reporte->collection();
        
        // 1. Capturamos el usuario logueado en el sistema
        $usuarioActivo = \Illuminate\Support\Facades\Auth::user();
        
        // 2. Extraemos el nombre viajando a la tabla responsables por su llave foránea
        $generadoPor = ($usuarioActivo && $usuarioActivo->responsable) 
            ? $usuarioActivo->responsable->nombre_apellido 
            : ($usuarioActivo ? $usuarioActivo->name : 'Sistema');

        // 3. Pasamos la variable al Blade y forzamos el formato horizontal (landscape)
        $pdf = Pdf::loadView('pdf.reporte-consolidado', [
            'items' => $items, 
            'filtros' => $filtros,
            'generado_por' => $generadoPor // <-- Mandamos el nombre de quien opera
        ])->setPaper('letter', 'landscape'); // Conserva la estética simétrica de columnas
        
        return response()->streamDownload(fn () => print($pdf->output()), "busqueda_especifica.pdf");
    }
}