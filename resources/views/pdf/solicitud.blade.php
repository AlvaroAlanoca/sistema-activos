<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Solicitud de Activo - {{ $numero_solicitud }}</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 11px; color: #333; }
        
        /* ESTILOS DEL ENCABEZADO (Mantenidos de tu Acta) */
        .header-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .header-table td { border-bottom: 3px solid #1e3a8a; padding-bottom: 12px; }
        .logo-cell { width: 25%; vertical-align: middle; }
        .title-cell { width: 75%; text-align: right; vertical-align: bottom; }
        .institucion { color: #1e3a8a; margin: 0; font-size: 18px; font-weight: bold; letter-spacing: 1px; }
        .tipo-acta { margin: 5px 0 0 0; font-size: 14px; font-weight: bold; color: #555; background-color: #ecf0f1; display: inline-block; padding: 5px 15px; border-radius: 4px; }

        .section-title { background-color: #60a5fa; color: white; padding: 5px; font-weight: bold; margin-top: 15px; margin-bottom: 5px; width: max-content; padding-right: 15px; border-radius: 0 10px 10px 0; }
        .info-table { width: 100%; border-collapse: collapse; margin-bottom: 10px; border: 1px solid #bdc3c7; table-layout: fixed; }
        .info-table td { padding: 5px; border-bottom: 1px solid #ecf0f1; vertical-align: top; word-wrap: break-word; }
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
        .footer-date { text-align: right; font-size: 9pt; margin-top: 40px; border-top: 1px solid #bdc3c7; padding-top: 5px; }
        
        .estado-badge { font-weight: bold; padding: 3px 8px; border-radius: 4px; color: white; background-color: #f59e0b; }
    </style>
</head>
<body>

    <table class="header-table">
        <tr>
            <td class="logo-cell">
                <img src="{{ public_path('img/logo.png') }}" style="height: 65px; width: auto;" alt="Logo Institucional">
            </td>
            <td class="title-cell">
                <h2 class="institucion">DIRECCIÓN DEPARTAMENTAL DE EDUCACIÓN LA PAZ</h2>
                <p class="tipo-acta">FORMULARIO DE SOLICITUD DE ACTIVO FIJO</p>
            </td>
        </tr>
    </table>

    <table style="width: 100%; margin-top: 10px; margin-bottom: 20px;">
        <tr>
            <td style="width: 50%;"><strong>Estado del Trámite:</strong> <span class="estado-badge">{{ $solicitud->estado }}</span></td>
            <td style="width: 50%; text-align: right;"><strong>Número de Solicitud:</strong> {{ $numero_solicitud }}</td>
        </tr>
        <tr>
            <td style="width: 50%;"><strong>Fecha de Petición:</strong> {{ $solicitud->created_at->format('d/m/Y H:i') }}</td>
            <td style="width: 50%; text-align: right;"></td>
        </tr>
    </table>

    <div class="section-title">Datos Del Funcionario Solicitante</div>
    <table class="info-table">
        <colgroup>
            <col style="width: 20%;"> <col style="width: 30%;"> <col style="width: 15%;"> <col style="width: 35%;">
        </colgroup>
        <tr>
            <td class="label">Apellidos y Nombres:</td>
            <td colspan="3">{{ $solicitante ? $solicitante->nombre_apellido : 'N/D' }}</td>
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

    <div class="section-title">Detalle del Activo Requerido</div>
    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 20%;">Código de Activo</th>
                <th style="width: 30%;">Categoría / Tipo</th>
                <th style="width: 50%;">Descripción Técnica del Bien</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="text-align: center; font-weight: bold;">{{ $solicitud->bien->codigo }}</td>
                <td style="text-align: center;">{{ $solicitud->bien->tipoBien?->descripcion ?? 'N/D' }}</td>
                <td>{{ $solicitud->bien->descripcion }}</td>
            </tr>
        </tbody>
    </table>

    <div class="section-title">Justificación de la Solicitud</div>
    <div style="margin-top: 10px; padding: 15px; border: 1px solid #bdc3c7; background-color: #f8fafc; text-align: justify; line-height: 1.5;">
        {{ $solicitud->motivo }}
    </div>

    <table class="tabla-firmas">
        <tr>
            <td>
                <span class="firma-titulo">SOLICITADO POR:</span>
                <div class="linea">
                    <span class="nombre-firma">{{ $solicitante ? $solicitante->nombre_apellido : 'N/D' }}</span>
                    <span class="cargo">{{ $solicitante?->oficinaCargo?->cargo?->descripcion ?? 'Funcionario Responsable' }}</span>
                </div>
            </td>

            <td class="spacer"></td>

            <td>
                <span class="firma-titulo">Vo.Bo. SELLO DE RECEPCIÓN:</span>
                <div class="linea" style="border-top: none; min-height: 60px;">
                    </div>
            </td>
        </tr>
    </table>

    <div class="footer-date">
        <p>
            Documento generado por: <strong>{{ auth()->user()?->responsable?->nombre_apellido ?? auth()->user()?->name ?? 'Sistema' }}</strong> 
            | Sistema DDELPZ - {{ now()->format('d/m/Y H:i') }}
        </p>
    </div>

</body>
</html>