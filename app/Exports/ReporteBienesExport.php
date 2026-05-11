<?php

namespace App\Exports;

use App\Models\Acta;
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

    public function __construct(array $filtros)
    {
        $this->filtros = $filtros;
    }

public function collection()
    {
        $query = Acta::with([
            'responsable.oficinaCargo.oficina', 
            'responsable.oficinaCargo.cargo', 
            'items.bien.tipoBien'
        ])->whereIn('tipo', ['ENTREGA', 'TRANSFERENCIA INTERNA', 'DEVOLUCION']);

        // 1. Filtros de Fechas
        if (!empty($this->filtros['fecha_inicio'])) {
            $query->whereDate('created_at', '>=', $this->filtros['fecha_inicio']);
        }
        if (!empty($this->filtros['fecha_fin'])) {
            $query->whereDate('created_at', '<=', $this->filtros['fecha_fin']);
        }
        
        // 2. Filtro de Responsable
        if (!empty($this->filtros['responsable_id'])) {
            $query->where('id_responsables', $this->filtros['responsable_id']);
        }

        // 3. CASCADA DE BIENES (Inteligente)
        // Busca desde lo más específico hasta lo más general usando elseif
        if (!empty($this->filtros['bien_id'])) {
            // Si eligió un bien específico, solo buscamos ese.
            $query->whereHas('items', function ($q) {
                $q->where('id_bienes', $this->filtros['bien_id']);
            });
        } elseif (!empty($this->filtros['tipo_bien_id'])) {
            // Si dejó el bien en blanco pero eligió un tipo, buscamos todos los de ese tipo.
            $query->whereHas('items.bien', function ($q) {
                $q->where('id_tipo_bien', $this->filtros['tipo_bien_id']);
            });
        } elseif (!empty($this->filtros['rubro_id'])) { 
            // Si solo eligió el rubro, traemos todos los bienes de ese rubro.
            $query->whereHas('items.bien.tipoBien', function ($q) {
                $q->where('id_rubro', $this->filtros['rubro_id']); 
            });
        }

        $actas = $query->get();
        $filasAplanadas = collect();

        // Extraer los filtros de forma segura (usando '?? null' evitamos errores si el campo quedó en blanco)
        $f_rubro = $this->filtros['rubro_id'] ?? null;
        $f_tipo  = $this->filtros['tipo_bien_id'] ?? null;
        $f_bien  = $this->filtros['bien_id'] ?? null;

        foreach ($actas as $acta) {
            foreach ($acta->items as $item) {
                
                // Si el filtro está vacío (empty), pasa automáticamente (true). 
                // Si tiene datos, verifica que coincida exactamente.
                $pasaFiltroRubro = empty($f_rubro) || 
                                   ($item->bien && $item->bien->tipoBien && $item->bien->tipoBien->id_rubro == $f_rubro);
                                   
                $pasaFiltroTipo = empty($f_tipo) || 
                                  ($item->bien && $item->bien->id_tipo_bien == $f_tipo);

                $pasaFiltroBien = empty($f_bien) || 
                                  ($item->id_bienes == $f_bien);

                // Solo si cumple con los filtros que SÍ se llenaron, lo añadimos al reporte
                if ($pasaFiltroRubro && $pasaFiltroTipo && $pasaFiltroBien) {
                    $filasAplanadas->push([
                        'acta' => $acta,
                        'item' => $item,
                    ]);
                }
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
        // Combinar celdas para el título (A1 a H1)
        $sheet->mergeCells('A1:I1');

        return [
            // Estilo del título principal
            1 => [
                'font' => [
                    'bold' => true, 
                    'size' => 16,
                    'color' => ['argb' => 'FF333333']
                ], 
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ]
            ],
            // Estilo de la cabecera de la tabla
            3 => [
                'font' => [
                    'bold' => true,
                    'size' => 12
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'color' => ['argb' => 'FFF2F2F2'] 
                ]
            ],
        ];
    }
}