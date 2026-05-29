<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Acta de Entrega de Combustible - Vale Nº {{ $vale->nro_vale }}</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 12px; color: #333; line-height: 1.5; }
        
        /* ENCABEZADO */
        .header-table { width: 100%; border-collapse: collapse; margin-bottom: 25px; }
        .header-table td { border-bottom: 2px solid #1e3a8a; padding-bottom: 10px; }
        .logo-cell { width: 20%; vertical-align: middle; }
        .title-cell { width: 80%; text-align: right; vertical-align: bottom; }
        .institucion { color: #1e3a8a; margin: 0; font-size: 15px; font-weight: bold; }
        .titulo-reporte { margin: 5px 0 0 0; font-size: 13px; font-weight: bold; color: #475569; text-transform: uppercase; }

        .titulo-principal { text-align: center; font-size: 14px; font-weight: bold; margin: 25px 0; text-transform: uppercase; letter-spacing: 0.5px; }

        /* CUERPO DEL ACTA */
        .parrafo-contexto { text-align: justify; margin-bottom: 20px; font-size: 12px; }
        
        /* TABLA DE DETALLES */
        .details-table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        .details-table th { background-color: #f1f5f9; text-align: left; padding: 8px; border: 1px solid #cbd5e1; font-size: 11px; text-transform: uppercase; color: #1e293b; width: 35%; }
        .details-table td { padding: 8px; border: 1px solid #cbd5e1; font-size: 12px; }

        /* SECCIÓN DE FIRMAS */
        .container-firmas { width: 100%; margin-top: 80px; table-layout: fixed; }
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
                <p class="titulo-reporte">SISTEMA DE CONTROL PATRIMONIAL Y CONTRATOS</p>
            </td>
        </tr>
    </table>

    <div class="titulo-principal">
        ACTA DE CONFORMIDAD DE DESPACHO DE COMBUSTIBLE<br>
        VALE Nº {{ $vale->nro_vale }}
    </div>

    <div class="parrafo-contexto">
        En cumplimiento a las normativas vigentes de control de activos y asignación de recursos del Estado, se suscribe la presente acta de conformidad de suministro de carburantes, detallando las especificaciones del volumen cargado y el activo motorizado asignado bajo la Dirección Departamental de Educación de La Paz:
    </div>

    <table class="details-table">
        <tr>
            <th>Fecha de Despacho:</th>
            <td>{{ \Carbon\Carbon::parse($vale->fecha_vale)->format('d/m/Y') }}</td>
        </tr>
        <tr>
            <th>Número de Vale Físico:</th>
            <td style="font-weight: bold; color: #1e3a8a;">{{ $vale->nro_vale }}</td>
        </tr>
        <tr>
            <th>Vehículo Asignado (Placa):</th>
            <td style="font-weight: bold;">{{ $vale->vehiculo?->placa ?? 'N/D' }}</td>
        </tr>
        <tr>
            <th>Descripción del Vehículo:</th>
            <td>{{ $vale->vehiculo?->descripcion ?? 'Sin descripción registrada' }}</td>
        </tr>
        <tr>
            <th>Volumen Cargado:</th>
            <td style="font-weight: bold;">{{ number_format($vale->cantidad_litros, 2) }} Litros</td>
        </tr>
        <tr>
            <th>Proveedor / Contrato Adjudicado:</th>
            <td>{{ $vale->servicio?->empresa ?? 'N/D' }} <br><small style="color: #64748b;">CUCE: {{ $vale->servicio?->cuce ?? 'N/D' }}</small></td>
        </tr>
        <tr>
            <th>Funcionario Responsable del Registro:</th>
            <td>{{ $vale->user?->responsable?->nombre_apellido ?? $vale->user?->name ?? 'Administrador del Sistema' }}</td>
        </tr>
    </table>

    <div class="parrafo-contexto" style="margin-top: 20px;">
        En señal de conformidad con los datos expuestos, la cantidad de combustible detallada y los saldos verificados en el portal informático, firman al pie de la presente constancia:
    </div>

    <table class="container-firmas">
        <tr>
            <td class="espacio-firma">
                <div class="linea-firma">
                    <strong>Entregado Por (Firma):</strong><br>
                    
                    <span style="font-weight: bold; text-transform: uppercase; font-size: 11px;">
                        {{ auth()->user()?->responsable?->nombre_apellido ?? auth()->user()?->name ?? 'Administrador de Activos' }}
                    </span><br>
                    
                    <span style="font-size: 10px; color: #475569; display: block; margin-top: 2px;">
                        Encargado de Suministros / SEDUCA
                    </span>
                </div>
            </td>
            <td class="espacio-firma">
                <div class="linea-firma">
                    <strong>Recibido Por (Firma):</strong><br>
                    
                    <span style="font-weight: bold; font-size: 11px;">
                        Conductor / Servidor Público Responsable
                    </span><br>
                    
                    <span style="font-size: 10px; color: #475569; display: block; margin-top: 2px;">
                        Vehículo Placa: {{ $vale->vehiculo?->placa ?? '_______' }}
                    </span>
                </div>
            </td>
        </tr>
    </table>
    <div class="footer">
        Copia de Respaldo Institucional | Impreso en fecha: {{ $fecha_impresion }} | Portal de Control DDELPZ
    </div>

</body>
</html>