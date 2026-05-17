<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte Consolidado de Bienes</title>
    <style>
        /* Sugerencia: Si las columnas se ven muy apretadas, puedes añadir 'landscape' aquí */
        @page { margin: 30px; } 
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 10px; color: #333; }
        
        /* ESTILOS DEL ENCABEZADO INSTITUCIONAL */
        .header-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .header-table td { border-bottom: 3px solid #1e3a8a; padding-bottom: 12px; }
        .logo-cell { width: 25%; vertical-align: middle; }
        .title-cell { width: 75%; text-align: right; vertical-align: bottom; }
        .institucion { color: #1e3a8a; margin: 0; font-size: 16px; font-weight: bold; letter-spacing: 1px; }
        .tipo-reporte { margin: 5px 0 0 0; font-size: 12px; font-weight: bold; color: #555; background-color: #ecf0f1; display: inline-block; padding: 5px 15px; border-radius: 4px; text-transform: uppercase; }

        /* ESTILOS DE LA TABLA */
        .table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .table th { background-color: #ecf0f1; padding: 8px; border: 1px solid #bdc3c7; font-weight: bold; text-align: center; font-size: 10px; }
        .table td { padding: 6px; border: 1px solid #bdc3c7; text-align: center; font-size: 9px; word-wrap: break-word; }
        
        .footer { text-align: right; font-size: 8px; margin-top: 20px; color: #7f8c8d; border-top: 1px solid #bdc3c7; padding-top: 5px; }
    </style>
</head>
<body>

    <table class="header-table">
        <tr>
            <td class="logo-cell">
                <img src="{{ public_path('img/logo.png') }}" style="height: 55px; width: auto;" alt="Logo Institucional">
            </td>
            <td class="title-cell">
                <h2 class="institucion">CONTROL DE BIENES Y SERVICIOS DDELPZ</h2>
                <p class="tipo-reporte">Reporte Consolidado de Activos</p>
            </td>
        </tr>
    </table>

    <table class="table">
        <thead>
            <tr>
                <th style="width: 15%;">Funcionario</th>
                <th style="width: 5%;">Nro. Ítem</th>
                <th style="width: 12%;">Cargo</th>
                <th style="width: 13%;">Oficina</th>
                <th style="width: 10%;">Código</th>
                <th style="width: 20%;">Descripción del Bien</th>
                <th style="width: 7%;">Costo</th>
                <th style="width: 8%;">Movimiento</th>
                <th style="width: 10%;">Fecha</th>
            </tr>
        </thead>
        <tbody>
            @forelse($items as $fila)
                @php
                    $acta = $fila['acta'];
                    $item = $fila['item'];
                    $responsable = $acta->responsable;
                @endphp
                <tr>
                    <td>{{ $responsable ? $responsable->nombre_apellido : 'N/D' }}</td>
                    <td>{{ $responsable ? $responsable->numero_item : 'N/D' }}</td>
                    <td>{{ $responsable?->oficinaCargo?->cargo?->descripcion ?? 'N/D' }}</td>
                    <td>{{ $responsable?->oficinaCargo?->oficina?->descripcion ?? 'N/D' }}</td>
                    <td>{{ $item->bien ? $item->bien->codigo : 'N/D' }}</td>
                    <td style="text-align: left;">{{ $item->bien ? $item->bien->descripcion : 'N/D' }}</td>
                    <td>Bs. {{ $item->bien ? number_format($item->bien->costo, 2) : '0.00' }}</td>
                    <td>{{ $acta->tipo }}</td>
                    <td>{{ $acta->created_at ? $acta->created_at->format('d/m/Y') : 'N/D' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" style="padding: 20px; font-size: 12px;">No se encontraron registros con los filtros seleccionados.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

   <div class="footer">
        Generado por: <strong>{{ $generado_por }}</strong> | Sistema  DDELPZ el {{ now()->format('d/m/Y H:i') }}
    </div>

</body>
</html>