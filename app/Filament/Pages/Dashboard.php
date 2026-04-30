<?php

namespace App\Filament\Pages;

use App\Models\Responsable;
use App\Models\ActaItem;
use App\Models\Acta;
use App\Models\Bien;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Placeholder;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Actions\Action;
use Illuminate\Support\HtmlString;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Notifications\Notification;

class Dashboard extends BaseDashboard
{
    protected static ?string $title = 'Panel de Control';

    // Nombre que aparece en el menú lateral
    protected static ?string $navigationLabel = 'Panel de Control';

    // Opcional: Cambiar el icono por una casa para que se vea más profesional
    protected static ?string $navigationIcon = 'heroicon-o-home';
    // Aquí definimos los botones que aparecen arriba a la derecha en el inicio
    protected function getHeaderActions(): array
    {
        return [
            Action::make('devolver_bienes_rapido')
                ->label('Devolución de Bienes')
                ->icon('heroicon-o-arrow-path')
                ->color('warning')
                ->modalHeading('Asistente de Devolución')
                ->modalDescription('Busque al funcionario para procesar la devolución de sus activos e imprimir el comprobante.')
                ->modalSubmitActionLabel('Procesar e Imprimir PDF')
                ->form([
                    Select::make('id_responsables')
                        ->label('Buscar Funcionario')
                        // Cargamos todos los responsables para el buscador
                        ->options(Responsable::pluck('nombre_apellido', 'idresponsables'))
                        ->searchable()
                        ->live() // Recarga el formulario al seleccionar
                        ->required(),

                    Placeholder::make('bienes_asignados')
                        ->label('Bienes que se van a devolver:')
                        // Generamos HTML en vivo dependiendo de a quién seleccione
                        ->content(function (\Filament\Forms\Get $get) {
                            $id = $get('id_responsables');
                            
                            if (!$id) {
                                return new HtmlString('<span style="color: gray;">Seleccione un funcionario primero...</span>');
                            }

                            // Buscamos los bienes entregados a este responsable
                            $bienes = ActaItem::whereHas('acta', fn($q) => $q->where('id_responsables', $id))
                                ->whereHas('bien', fn($q) => $q->where('estado', 'ENTREGADO'))
                                ->get();

                            if ($bienes->isEmpty()) {
                                return new HtmlString('<span style="color: red; font-weight: bold;">Este funcionario no tiene bienes pendientes por devolver.</span>');
                            }

                            // Armamos una lista visual de los bienes
                            $html = '<ul style="list-style-type: disc; padding-left: 20px;">';
                            foreach($bienes as $item) {
                                $html .= "<li>[{$item->bien->codigo}] - {$item->bien->descripcion}</li>";
                            }
                            $html .= '</ul>';

                            return new HtmlString($html);
                        }),
                ])
                ->action(function (array $data) {
                    $idResponsable = $data['id_responsables'];

                    // 1. Obtenemos los ítems a devolver
                    $itemsPendientes = ActaItem::with('bien')
                        ->whereHas('acta', fn($q) => $q->where('id_responsables', $idResponsable))
                        ->whereHas('bien', fn($q) => $q->where('estado', 'ENTREGADO'))
                        ->get();

                    if ($itemsPendientes->isEmpty()) {
                        Notification::make()->title('Sin bienes')->warning()->send();
                        return;
                    }

                    // 2. Creamos el Acta de Devolución
                    $nuevaActa = Acta::create([
                        'tipo' => 'DEVOLUCION',
                        'numero_acta' => 'DEV-' . now()->format('Ymd-His'),
                        'id_responsables' => $idResponsable,
                    ]);

                    // 3. Procesamos los bienes y los liberamos
                    foreach ($itemsPendientes as $item) {
                        ActaItem::create([
                            'id_acta' => $nuevaActa->idacta,
                            'id_bienes' => $item->id_bienes,
                            'estado' => 'Bueno', 
                        ]);
                        Bien::where('idbienes', $item->id_bienes)->update(['estado' => 'DISPONIBLE']);
                    }

                    Notification::make()->title('Devolución procesada exitosamente')->success()->send();

                    // 4. Descargamos el PDF automáticamente
                    return response()->streamDownload(function () use ($nuevaActa) {
                        echo Pdf::loadView('pdf.acta', [
                            'acta' => $nuevaActa,
                            'items' => $nuevaActa->items()->with('bien')->get(),
                        ])->output();
                    }, "Devolucion_{$nuevaActa->numero_acta}.pdf");
                })
        ];
    }
}