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
            'items.bien'
        ])->whereIn('tipo', ['ENTREGA', 'TRANSFERENCIA INTERNA', 'DEVOLUCION']);

        if (!empty($this->filtros['fecha_inicio'])) {
            $query->whereDate('created_at', '>=', $this->filtros['fecha_inicio']);
        }
        
        if (!empty($this->filtros['fecha_fin'])) {
            $query->whereDate('created_at', '<=', $this->filtros['fecha_fin']);
        }
        
        if (!empty($this->filtros['responsable_id'])) {
            $query->where('id_responsables', $this->filtros['responsable_id']);
        }
        $actas = $query->get();
        $filasAplanadas = collect();

        foreach ($actas as $acta) {
            foreach ($acta->items as $item) {
                $filasAplanadas->push([
                    'acta' => $acta,
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
            ['SISTEMA DE CONTROL DE BIENES Y SERVICIOS'],
            [''], // Fila en blanco
            [
                'Nombre del Funcionario',
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
        $sheet->mergeCells('A1:H1');

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