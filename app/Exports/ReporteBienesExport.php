<?php

namespace App\Exports;

use App\Models\Acta;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;

// ¡Esta es la línea clave que causaba el error!
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet; 

class ReporteBienesExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected array $filtros;
    protected string $generadoPor;

public function __construct(array $filtros, string $generadoPor = 'Sistema')
    {
        $this->filtros = $filtros;
        $this->generadoPor = $generadoPor; 
    }

public function collection()
    {
        // Apuntamos directamente a ActaItem para cruzar la información exacta.
        $query = \App\Models\ActaItem::query()
            ->with([
                'acta.responsable.oficinaCargo.oficina', 
                'acta.responsable.oficinaCargo.cargo', 
                'bien.tipoBien'
            ])
            ->select('acta_items.*') // CRUCIAL: Evita que el JOIN sobreescriba los datos del ítem
            ->join('actas as a', 'a.idacta', '=', 'acta_items.id_acta')
            // 1. EL FILTRO MAESTRO : Solo el ÚLTIMO movimiento de cada bien
            ->whereRaw('a.idacta = (SELECT MAX(a2.idacta) FROM acta_items as ai2 INNER JOIN actas as a2 ON a2.idacta = ai2.id_acta WHERE ai2.id_bienes = acta_items.id_bienes)')
            // 2. Si ese último movimiento fue una devolución, el bien está libre (no se reporta)
            ->where('a.tipo', '!=', 'DEVOLUCION');

        // 3. Filtro por Responsable (Si se seleccionó uno en el formulario)
        if (!empty($this->filtros['responsable_id'])) {
            $query->where('a.id_responsables', $this->filtros['responsable_id']);
        }

        // 4. Filtros de Fechas (Aplicados a la fecha de esa última acta)
        if (!empty($this->filtros['fecha_inicio'])) {
            $fechaInicio = Carbon::parse($this->filtros['fecha_inicio'])->startOfDay();
            $query->where('a.created_at', '>=', $fechaInicio);
        }
        
        if (!empty($this->filtros['fecha_fin'])) {
            $fechaFin = Carbon::parse($this->filtros['fecha_fin'])->endOfDay();
            $query->where('a.created_at', '<=', $fechaFin);
        }

        // 5. CASCADA DE BIENES (Inteligente)
        if (!empty($this->filtros['bien_id'])) {
            $query->where('acta_items.id_bienes', $this->filtros['bien_id']);
        } elseif (!empty($this->filtros['tipo_bien_id'])) {
            $query->whereHas('bien', function ($q) {
                $q->where('id_tipo_bien', $this->filtros['tipo_bien_id']);
            });
        } elseif (!empty($this->filtros['rubro_id'])) { 
            $query->whereHas('bien.tipoBien', function ($q) {
                $q->where('id_rubro', $this->filtros['rubro_id']); 
            });
        }

        // Ejecutamos la consulta final
        $itemsFiltrados = $query->get();
        $filasAplanadas = collect();

        // Estructuramos la respuesta tal cual la espera tu método map()
        foreach ($itemsFiltrados as $item) {
            // Verificamos que la relación del acta no sea nula por seguridad
            if ($item->acta) {
                $filasAplanadas->push([
                    'acta' => $item->acta,
                    'item' => $item,
                ]);
            }
        }

        return $filasAplanadas;
    }

public function map($fila): array
    {
        $acta = $fila['acta'];
        $item = $fila['item'];
        $responsable = $acta->responsable;

        return [
            $responsable ? $responsable->nombre_apellido : 'N/D',
            $responsable ? $responsable->numero_item : 'N/D', // <-- NUEVA LÍNEA
            $responsable?->oficinaCargo?->cargo?->descripcion ?? 'N/D',
            $responsable?->oficinaCargo?->oficina?->descripcion ?? 'N/D',
            $item->bien ? $item->bien->codigo : 'N/D',
            $item->bien ? $item->bien->descripcion : 'N/D',
            $item->bien ? $item->bien->costo : '0.00',
            $acta->tipo,
            $acta->created_at ? $acta->created_at->format('d/m/Y H:i') : 'N/D',
        ];
    }

    public function headings(): array
    {

        return [
            ['CONTROL DE BIENES Y SERVICIOS DDELPZ'],
            ['Reporte generado por: ' . $this->generadoPor], // <-- 3. Imprimimos el nombre aquí
            [''], 
            [
                'Nombre del Funcionario',
                'Nro. Ítem',
                'Cargo',
                'Oficina',
                'Código de Bien',
                'Descripción del Bien',
                'Costo',
                'Tipo de Movimiento',
                'Fecha del Movimiento',
            ]
        ];
    }

 public function styles(Worksheet $sheet)
    {
        // Combinar celdas para el título principal (Fila 1)
        $sheet->mergeCells('A1:I1');
        
        // Combinar celdas para los datos del creador del reporte (Fila 2)
        $sheet->mergeCells('A2:I2');

        return [
            // Estilo del título principal (Fila 1)
            1 => [
                'font' => [
                    'bold' => true, 
                    'size' => 16,
                    'color' => ['argb' => 'FF1E3A8A'] 
                ], 
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ]
            ],
            // Estilo de los metadatos de creación (Fila 2)
            2 => [
                'font' => [
                    'italic' => true,
                    'size' => 10,
                    'color' => ['argb' => 'FF555555']
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ]
            ],
            // Titulos de tabla
            4 => [
                'font' => [
                    'bold' => true,
                    'size' => 11,
                    'color' => ['argb' => 'FF2C3E50']
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'color' => ['argb' => 'FFF2F2F2'] 
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                ]
            ],
        ];
    }
}