<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Acta de Baja de Activo Fijo - {{ $baja->bien?->codigo }}</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 12px; color: #333; line-height: 1.6; }
        
        /* ENCABEZADO INSTITUCIONAL */
        .header-table { width: 100%; border-collapse: collapse; margin-bottom: 25px; }
        .header-table td { border-bottom: 2px solid #1e3a8a; padding-bottom: 10px; }
        .logo-cell { width: 20%; vertical-align: middle; }
        .title-cell { width: 80%; text-align: right; vertical-align: bottom; }
        .institucion { color: #1e3a8a; margin: 0; font-size: 15px; font-weight: bold; }
        .titulo-reporte { margin: 5px 0 0 0; font-size: 12px; font-weight: bold; color: #475569; text-transform: uppercase; }

        .titulo-principal { text-align: center; font-size: 14px; font-weight: bold; margin: 30px 0 20px 0; text-transform: uppercase; letter-spacing: 0.5px; }

        .parrafo-contexto { text-align: justify; margin-bottom: 15px; font-size: 12px; text-indent: 30px; }
        
        /* TABLA DE ESPECIFICACIONES TÉCNICAS */
        .details-table { width: 100%; border-collapse: collapse; margin: 25px 0; }
        .details-table th { background-color: #f1f5f9; text-align: left; padding: 8px; border: 1px solid #cbd5e1; font-size: 11px; text-transform: uppercase; color: #1e293b; width: 35%; }
        .details-table td { padding: 8px; border: 1px solid #cbd5e1; font-size: 12px; }

        /* SECCIÓN DE FIRMAS */
        .container-firmas { width: 100%; margin-top: 90px; table-layout: fixed; }
        .espacio-firma { text-align: center; vertical-align: bottom; }
        .linea-firma { width: 80%; margin: 0 auto; border-top: 1px solid #333; padding-top: 5px; font-size: 11px; }
        
        .footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 8px; color: #64748b; border-top: 1px solid #e2e8f0; padding-top: 4px; }
    </style>
</head>
<body>

    <table class="header-table">
        <tr>
            <td class="logo-cell">
                <img src="{{ public_path('img/logo.png') }}" style="height: 50px; width: auto;" alt="Logo">
            </td>
            <td class="title-cell">
                <h2 class="institucion">DIRECCIÓN DEPARTAMENTAL DE EDUCACIÓN LA PAZ</h2>
                <p class="titulo-reporte">SISTEMA DE CONTROL PATRIMONIAL Y ACTIVOS FIJOS</p>
            </td>
        </tr>
    </table>

    <div class="titulo-principal">
        ACTA DE BAJA DEFECTIVA DE BIEN PATRIMONIAL
    </div>

    <div class="parrafo-contexto">
        En conformidad con los lineamientos estipulados, se procede a formalizar la desincorporación física y el retiro contable del inventario institucional de la Dirección Departamental de Educación de La Paz, detallando las especificaciones técnicas del activo fijo descrito a continuación:
    </div>

    <table class="details-table">
        <tr>
            <th>Código de Activo Fijo:</th>
            <td style="font-weight: bold; color: #1e3a8a; font-size: 13px;">{{ $baja->bien?->codigo }}</td>
        </tr>
        <tr>
            <th>Rubro / Categoría:</th>
            <td>{{ $baja->bien?->tipoBien?->descripcion ?? 'No asignado' }}</td>
        </tr>
        <tr>
            <th>Descripción del Bien:</th>
            <td style="font-weight: bold;">{{ $baja->bien?->descripcion }}</td>
        </tr>
        <tr>
            <th>Valor / Costo de Adquisición:</th>
            <td>Bs. {{ number_format($baja->bien?->costo, 2) }}</td>
        </tr>
        <tr>
            <th>Motivo de la Desincorporación:</th>
            <td style="color: #b91c1c; font-weight: bold;">{{ $baja->motivo_baja }}</td>
        </tr>
        <tr>
            <th>Fecha de Aprobación de Baja:</th>
            <td>{{ \Carbon\Carbon::parse($baja->fecha_aprobacion)->format('d/m/Y') }}</td>
        </tr>
    </table>

    <div class="parrafo-contexto">
        Se deja constancia de que a partir de la fecha de aprobación del presente documento, el activo mencionado queda completamente inhabilitado para operaciones administrativas institucionales, quedando bajo responsabilidad de la Unidad de Almacenes y Activos Fijos su destino definitivo o destrucción física según corresponda por ley.
    </div>

    <div class="parrafo-contexto" style="margin-top: 15px;">
        Para constancia del proceso de auditoría patrimonial, firman los servidores públicos intervinientes en la presente diligencia:
    </div>

    <table class="container-firmas">
        <tr>
            <td class="espacio-firma">
                <div class="linea-firma">
                    <strong>Procesado Por:</strong><br>
                    <span style="text-transform: uppercase; font-weight: bold;">
                        {{ auth()->user()?->responsable?->nombre_apellido ?? auth()->user()?->name ?? 'Técnico de Activos' }}
                    </span><br>
                    Unidad de Activos Fijos - DDELPZ
                </div>
            </td>

        </tr>
    </table>

    <div class="footer">
        Respaldo de Exclusión de Inventario | Impreso en fecha: {{ $fecha_impresion }} | Portal de Control Activos DDELPZ
    </div>

</body>
</html>