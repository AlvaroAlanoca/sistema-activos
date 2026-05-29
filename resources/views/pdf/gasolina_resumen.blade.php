<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Resumen de Consumo por Placa</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 12px; color: #333; }
        .header-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .header-table td { border-bottom: 3px solid #1e3a8a; padding-bottom: 12px; }
        .logo-cell { width: 25%; vertical-align: middle; }
        .title-cell { width: 75%; text-align: right; vertical-align: bottom; }
        .institucion { color: #1e3a8a; margin: 0; font-size: 18px; font-weight: bold; }
        .subtitulo { margin: 5px 0 0 0; font-size: 14px; font-weight: bold; color: #555; }
        
        .info-panel { background-color: #f8fafc; border: 1px solid #e2e8f0; padding: 10px; margin-bottom: 20px; text-align: center; font-size: 14px; }
        
        .report-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .report-table th { background-color: #1e3a8a; color: white; padding: 8px; border: 1px solid #cbd5e1; }
        .report-table td { padding: 8px; border: 1px solid #cbd5e1; text-align: center; }
        
        .total-row td { background-color: #f1f5f9; font-weight: bold; font-size: 14px; color: #1e3a8a; }
        .footer { text-align: right; font-size: 9px; margin-top: 40px; border-top: 1px solid #cbd5e1; padding-top: 5px; }
    </style>
</head>
<body>

    <table class="header-table">
        <tr>
            <td class="logo-cell">
                <img src="{{ public_path('img/logo.png') }}" style="height: 60px; width: auto;" alt="Logo">
            </td>
            <td class="title-cell">
                <h2 class="institucion">DIRECCIÓN DEPARTAMENTAL DE EDUCACIÓN LA PAZ</h2>
                <p class="subtitulo">REPORTE CONSOLIDADO DE CONSUMO DE COMBUSTIBLE</p>
            </td>
        </tr>
    </table>

<div class="info-panel" style="text-align: left; line-height: 1.6;">
    <strong>Período Evaluado:</strong> Del {{ $desde }} al {{ $hasta }}<br>
    <strong>Vehículos Incluidos:</strong> <span style="color: #1e3a8a; font-weight: bold;">{{ $placasFiltro }}</span>
</div>
<h3 style="font-size: 11px; margin-bottom: 5px; color: #1e3a8a;">ESTADO ACTUAL DE CONTRATOS / PROVEEDORES</h3>
    <table class="report-table" style="margin-top: 0; margin-bottom: 20px;">
        <thead>
            <tr>
                <th style="background-color: #f1f5f9; color: #333; width: 40%;">Proveedor (CUCE)</th>
                <th style="background-color: #f1f5f9; color: #333; width: 30%;">Litros Totales Contratados</th>
                <th style="background-color: #f1f5f9; color: #333; width: 30%;">Saldo Disponible Actual</th>
            </tr>
        </thead>
        <tbody>
            @forelse($contratos as $contrato)
            <tr>
                <td style="text-align: left;"><strong>{{ $contrato->empresa }}</strong><br><span style="font-size: 9px; color: #64748b;">CUCE: {{ $contrato->cuce }}</span></td>
                <td>{{ number_format($contrato->total_contrato, 2) }} Lts.</td>
                <td style="font-weight: bold; color: {{ $contrato->saldo_actual <= 500 ? '#b91c1c' : '#15803d' }};">
                    {{ number_format($contrato->saldo_actual, 2) }} Lts.
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="3">No se detectaron contratos asociados.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <h3 style="font-size: 11px; margin-bottom: 5px; color: #1e3a8a; margin-top: 20px;">DESGLOSE DE CONSUMO POR VEHÍCULO</h3>
    <table class="report-table">
        <thead>
            <tr>
                <th style="width: 10%;">Nº</th>
                <th style="width: 30%;">Placa del Vehículo</th>
                <th style="width: 30%;">Cantidad de Cargas (Vales)</th>
                <th style="width: 30%;">Volumen Total Consumido</th>
            </tr>
        </thead>
        <tbody>
            @forelse($resumen as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td style="font-weight: bold;">{{ $item->placa }}</td>
                <td>{{ $item->total_cargas }} veces</td>
                <td style="font-weight: bold; color: #b91c1c;">{{ number_format($item->total_litros, 2) }} Lts.</td>
            </tr>
            @empty
            <tr>
                <td colspan="4">No se encontraron registros de carga en este rango de fechas.</td>
            </tr>
            @endforelse
            
            <tr class="total-row">
                <td colspan="3" style="text-align: right; padding-right: 15px;">GRAN TOTAL LITROS DESPACHADOS:</td>
                <td>{{ number_format($granTotal, 2) }} Lts.</td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        Generado por: <strong>{{ auth()->user()?->responsable?->nombre_apellido ?? auth()->user()?->name ?? 'Sistema' }}</strong> 
        | Fecha de Impresión: {{ now()->format('d/m/Y H:i') }}
    </div>

</body>
</html>