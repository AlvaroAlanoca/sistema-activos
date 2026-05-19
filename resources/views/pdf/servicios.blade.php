<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Servicios</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; }
        
        /* Estilos de la nueva cabecera */
        .encabezado-tabla {
            width: 100%;
            margin-bottom: 20px;
            border-bottom: 2px solid #1E3A8A; /* Línea azul institucional */
            padding-bottom: 15px;
        }
        .encabezado-tabla td {
            border: none; /* Quitamos los bordes solo para esta tabla */
            padding: 0;
            vertical-align: middle;
        }
        .titulo { font-size: 16px; font-weight: bold; color: #1E3A8A; }
        .subtitulo { font-size: 12px; font-weight: bold; margin-top: 5px; }
        
        /* Estilos de la tabla de datos */
        .tabla-datos { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .tabla-datos th, .tabla-datos td { border: 1px solid #444; padding: 6px; text-align: left; }
        .tabla-datos th { background-color: #f3f4f6; font-weight: bold; font-size: 12px; }
        
        .badge-ddelpz { color: #059669; font-weight: bold; }
        .badge-sicoes { color: #d97706; font-weight: bold; }
                .footer-date { text-align: right; font-size: 9pt; margin-top: 40px; border-top: 1px solid #bdc3c7; padding-top: 5px; }
    </style>
</head>
<body>

    <table class="encabezado-tabla">
        <tr>
            <td width="20%">
                <img src="{{ public_path('img/logo.png') }}" width="90" alt="Logo">
            </td>
            
            <td width="60%" style="text-align: center;">
                <div class="titulo">SISTEMA DE CONTROL DE ACTIVOS FIJOS DDELPZ</div>
                <div class="subtitulo">REPORTE GENERAL: CATÁLOGO DE SERVICIOS Y CONTRATOS</div>
            </td>
            
            <td width="20%" style="text-align: right; font-size: 10px; color: #555;">
                Generado el:<br>
                <strong>{{ $fecha_generacion }}</strong>
            </td>
        </tr>
    </table>

    <table class="tabla-datos">
        <thead>
            <tr>
                <th width="8%">Origen</th>
                <th width="15%">CUCE</th>
                <th width="20%">Empresa Proveedora</th>
                <th width="27%">Descripción</th>
                <th width="10%">F. Inicio</th>
                <th width="10%">F. Final</th>
                <th width="10%">% Avance</th>
            </tr>
        </thead>
        <tbody>
            @foreach($servicios as $servicio)
            <tr>
                <td class="{{ $servicio->tipo === 'DDELPZ' ? 'badge-ddelpz' : 'badge-sicoes' }}">
                    {{ $servicio->tipo }}
                </td>
                <td>{{ $servicio->cuce }}</td>
                <td>{{ $servicio->empresa }}</td>
                <td>{{ Str::limit($servicio->descripcion, 80) }}</td>
                <td>{{ $servicio->fecha_inicio }}</td>
                <td>{{ $servicio->fecha_final }}</td>
                <td style="text-align: center;">{{ $servicio->porcentaje_avance ?? '0' }}%</td>
            </tr>
            @endforeach
        </tbody>
    </table>
<div class="footer-date">
        <p>
                {{-- Aqui esta la impresion de usuario --}}
            Generado por: <strong>{{ auth()->user()?->responsable?->nombre_apellido ?? auth()->user()?->name ?? 'Sistema' }}</strong> 
            | Sistema DDELPZ - {{ now()->format('d/m/Y H:i') }}
        </p>
    </div>
</body>
</html>