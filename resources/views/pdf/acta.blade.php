<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Acta de {{ $acta->tipo }} - {{ $acta->numero_acta }}</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 11px; color: #333; }
        .header { background-color: #1e3a8a; color: white; padding: 10px; text-align: center; font-weight: bold; font-size: 14px; text-transform: uppercase; }
        .section-title { background-color: #60a5fa; color: white; padding: 5px; font-weight: bold; margin-top: 15px; margin-bottom: 5px; width: max-content; padding-right: 15px; border-radius: 0 10px 10px 0; }
       .info-table { width: 100%; border-collapse: collapse; margin-bottom: 10px; border: 1px solid #bdc3c7; table-layout: fixed; }
        .info-table td { padding: 5px; border-bottom: 1px solid #ecf0f1; vertical-align: top; word-wrap: break-word; }
        /* Se quita el width fijo de aquí para controlarlo con colgroup */
        .label { font-weight: bold; text-align: right; padding-right: 10px; color: #2c3e50; }
        .items-table { width: 100%; border-collapse: collapse; margin-top: 10px; text-align: center; }
        .items-table th { background-color: #ecf0f1; padding: 8px; border: 1px solid #bdc3c7; font-weight: bold; }
        .items-table td { padding: 8px; border: 1px solid #bdc3c7; text-align: left; }
        
/* ESTILOS DE FIRMAS */
        .tabla-firmas { width: 100%; margin-top: 80px; border-collapse: collapse; border: none; table-layout: fixed; }
        .tabla-firmas td { width: 45%; text-align: center; vertical-align: top; border: none; padding: 0 10px; }
        .spacer { width: 10%; }
        .firma-titulo { font-weight: bold; margin-bottom: 60px; display: block; }
        .linea { border-top: 1px solid #000; padding-top: 8px; margin: 0 auto; width: 80%; min-height: 40px; }
        .nombre-firma { font-weight: bold; font-size: 11pt; display: block; }
        .cargo { font-size: 10pt; color: #333; display: block; margin-top: 2px; }
        .footer-date { text-align: right; font-size: 9pt; margin-top: 40px; }
    </style>
</head>
<body>

    {{-- ESTA LÍNEA ES LA SOLUCIÓN DEFINITIVA AL ERROR --}}
    @php
        $solicitante = $solicitante ?? null;
        $receptor = $receptor ?? null;
    @endphp

    <div class="header">
        FORMULARIO DE {{ $acta->tipo }} DE ACTIVOS FIJOS
    </div>

    <!-- SECCIÓN: DATOS DEL DOCUMENTO -->
    <table style="width: 100%; margin-top: 10px; margin-bottom: 20px;">
        <tr>
            <td style="width: 50%;"><strong>Tipo de Movimiento:</strong> {{ $acta->tipo }}</td>
            <td style="width: 50%; text-align: right;"><strong>Número de Acta:</strong> {{ $acta->numero_acta }}</td>
        </tr>
        <tr>
            <td style="width: 50%;"><strong>Fecha de Emisión:</strong> {{ optional($acta->created_at)->format('d/m/Y H:i') ?? now()->format('d/m/Y H:i') }}</td>
            <td style="width: 50%; text-align: right;"></td>
        </tr>
    </table>

 <div class="section-title">Datos Del Funcionario Solicitante</div>
    <table class="info-table">
        <colgroup>
            <col style="width: 20%;"> <col style="width: 30%;"> <col style="width: 15%;"> <col style="width: 35%;"> </colgroup>
        <tr>
            <td class="label">Apellidos y Nombres:</td>
            <td colspan="3">{{ $solicitante ? $solicitante->nombre_apellido : 'ADMINISTRADOR NO VINCULADO' }}</td>
        </tr>
        <tr>
            <td class="label">Nro. Documento:</td>
            <td>{{ $solicitante ? $solicitante->ci : 'N/D' }}</td>
            <td class="label">Nro. Ítem:</td>
            <td>{{ $solicitante ? $solicitante->numero_item : 'N/D' }}</td>
        </tr>
        <tr>
            <td class="label">Oficina:</td>
            <td>{{ $solicitante?->oficinaCargo?->oficina?->descripcion ?? 'N/D' }}</td>
            <td class="label">Cargo:</td>
            <td>{{ $solicitante?->oficinaCargo?->cargo?->descripcion ?? 'N/D' }}</td>
        </tr>
    </table>

    <div class="section-title">Datos Del Funcionario Receptor</div>
    <table class="info-table">
        <colgroup>
            <col style="width: 20%;">
            <col style="width: 30%;">
            <col style="width: 15%;">
            <col style="width: 35%;">
        </colgroup>
        <tr>
            <td class="label">Apellidos y Nombres:</td>
            <td colspan="3">{{ $receptor ? $receptor->nombre_apellido : 'N/D' }}</td>
        </tr>
        <tr>
            <td class="label">Nro. Documento:</td>
            <td>{{ $receptor ? $receptor->ci : 'N/D' }}</td>
            <td class="label">Nro. Ítem:</td>
            <td>{{ $receptor ? $receptor->numero_item : 'N/D' }}</td>
        </tr>
        <tr>
            <td class="label">Oficina:</td>
            <td>{{ $receptor?->oficinaCargo?->oficina?->descripcion ?? 'N/D' }}</td>
            <td class="label">Cargo:</td>
            <td>{{ $receptor?->oficinaCargo?->cargo?->descripcion ?? 'N/D' }}</td>
        </tr>
    </table>

    <!-- SECCIÓN: OBSERVACIONES -->
    @if($acta->observaciones)
    <div style="margin-top: 10px; padding: 10px; border: 1px solid #bdc3c7;">
        <strong>Observaciones:</strong><br>
        {{ $acta->observaciones }}
    </div>
    @endif

    <!-- SECCIÓN: ACTIVOS -->
    <div class="section-title">Activos Relacionados (Total: {{ $items->count() }})</div>
    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 15%;">Código</th>
                <th style="width: 70%;">Descripción del Bien</th>
                <th style="width: 15%;">Estado</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $item)
            <tr>
                <td style="text-align: center;">{{ $item->bien->codigo }}</td>
                <td>{{ $item->bien->descripcion }}</td>
                <td style="text-align: center;">{{ $item->estado ?? 'Bueno' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- SECCIÓN: FIRMAS  -->
    <table class="tabla-firmas">
        <tr>
            <td>
                <span class="firma-titulo">ENTREGUÉ CONFORME</span>
                <div class="linea">
                    @if($acta->tipo == 'ENTREGA' || $acta->tipo == 'TRANSFERENCIA INTERNA')
                        <span class="nombre-firma">{{ $solicitante ? $solicitante->nombre_apellido : auth()->user()->name }}</span>
                        <span class="cargo">{{ $solicitante?->oficinaCargo?->cargo?->descripcion ?? 'Encargado de Activos / Sistema' }}</span>
                    @else
                        <span class="nombre-firma">{{ $receptor ? $receptor->nombre_apellido : 'N/D' }}</span>
                        <span class="cargo">{{ $receptor?->oficinaCargo?->cargo?->descripcion ?? 'Funcionario Responsable' }}</span>
                    @endif
                </div>
            </td>

            <td class="spacer"></td>

            <td>
                <span class="firma-titulo">RECIBÍ CONFORME</span>
                <div class="linea">
                    @if($acta->tipo == 'ENTREGA' || $acta->tipo == 'TRANSFERENCIA INTERNA')
                        <span class="nombre-firma">{{ $receptor ? $receptor->nombre_apellido : 'N/D' }}</span>
                        <span class="cargo">{{ $receptor?->oficinaCargo?->cargo?->descripcion ?? 'Funcionario Responsable' }}</span>
                    @else
                        <span class="nombre-firma">{{ $solicitante ? $solicitante->nombre_apellido : auth()->user()->name }}</span>
                        <span class="cargo">{{ $solicitante?->oficinaCargo?->cargo?->descripcion ?? 'Encargado de Activos / Sistema' }}</span>
                    @endif
                </div>
            </td>
        </tr>
    </table>

    <div class="footer-date">
        <p>Documento generado por el Sistema DDELPZ - {{ date('Y') }}</p>
    </div>

</body>
</html>