<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte General de Servicios - DDELPZ</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 10px; color: #333; margin: 0; padding: 0; }
        
        /* ENCABEZADO INSTITUCIONAL */
        .header-table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        .header-table td { border-bottom: 3px solid #1e3a8a; padding-bottom: 10px; }
        .logo-cell { width: 20%; vertical-align: middle; }
        .title-cell { width: 80%; text-align: right; vertical-align: bottom; }
        .institucion { color: #1e3a8a; margin: 0; font-size: 16px; font-weight: bold; letter-spacing: 1px; }
        .titulo-reporte { margin: 5px 0 0 0; font-size: 12px; font-weight: bold; color: #475569; text-transform: uppercase; }

        /* METADATOS DEL REPORTE */
        .meta-table { width: 100%; margin-bottom: 15px; font-size: 9px; }
        
        /* TABLA PRINCIPAL DE REGISTROS */
        .report-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .report-table th { background-color: #f1f5f9; color: #1e293b; padding: 6px 4px; border: 1px solid #cbd5e1; font-weight: bold; font-size: 8px; text-transform: uppercase; }
        .report-table td { padding: 6px 4px; border: 1px solid #e2e8f0; vertical-align: middle; font-size: 9px; }
        
        /* ESTILOS DE TEXTO Y COLORES */
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .bold { font-weight: bold; }
        .text-primary { color: #1e3a8a; }
        
        /* COMPONENTES VISUALES COMPACTOS */
        .badge-origen { padding: 2px 4px; border-radius: 3px; font-weight: bold; font-size: 8px; color: white; }
        .bg-ddelpz { background-color: #10b981; }
        .bg-sicoes { background-color: #f59e0b; }
        
        .doc-tag { display: inline-block; background-color: #e2e8f0; color: #334155; padding: 2px 4px; border-radius: 2px; font-size: 7px; margin: 1px; font-weight: bold; }

        .footer { text-align: right; font-size: 8px; margin-top: 20px; border-top: 1px solid #cbd5e1; padding-top: 5px; color: #64748b; }
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
                <p class="titulo-reporte">REPORTE GENERAL DE CONTRATOS Y SERVICIOS</p>
            </td>
        </tr>
    </table>

    <table class="meta-table">
        <tr>
            <td><strong>Fecha de Generación:</strong> {{ $fecha_generacion }}</td>
            <td style="text-align: right;"><strong>Total Registros:</strong> {{ $servicios->count() }}</td>
        </tr>
    </table>

    <table class="report-table">
        <thead>
            <tr>
                <th style="width: 5%;">Origen</th>
                <th style="width: 10%;">Tipo Servicio</th>
                <th style="width: 9%;">CUCE / Código</th>
                <th style="width: 13%;">Empresa Proveedora</th>
                <th style="width: 14%;">Descripción</th>
                <th style="width: 6%;">F. Inicio</th>
                <th style="width: 6%;">F. Final</th>
                <th style="width: 6%;">Litros</th>
                <th style="width: 8%;">Monto Total</th>
                <th style="width: 5%;">Avance</th>
                <th style="width: 18%;">Respaldos Digitales</th>
            </tr>
        </thead>
        <tbody>
            @foreach($servicios as $servicio)
            <tr>
                <td class="text-center">
                    <span class="badge-origen {{ $servicio->tipo === 'DDELPZ' ? 'bg-ddelpz' : 'bg-sicoes' }}">
                        {{ $servicio->tipo }}
                    </span>
                </td>
                
                <td>
                    @if($servicio->tipo === 'DDELPZ' && $servicio->tipo_servicio)
                        {{ match($servicio->tipo_servicio) {
                            'COMBUSTIBLE' => 'Combustible',
                            'MANTENIMIENTO' => 'Mantenimiento',
                            'SEGUROS' => 'Seguros',
                            'CONSULTORIA' => 'Consultoría',
                            'OTROS' => 'Otros',
                            default => $servicio->tipo_servicio
                        } }}
                    @else
                        <span style="color: #94a3b8; font-style: italic;">No aplica</span>
                    @endif
                </td>
                
                <td class="text-center bold text-primary">{{ $servicio->cuce }}</td>
                
                <td class="bold">{{ $servicio->empresa }}</td>
                
                <td>{{ \Illuminate\Support\Str::limit($servicio->descripcion, 45) }}</td>

                <td class="text-center">
                    {{ $servicio->fecha_inicio ? \Carbon\Carbon::parse($servicio->fecha_inicio)->format('d/m/Y') : '--' }}
                </td>

                <td class="text-center">
                    {{ $servicio->fecha_final ? \Carbon\Carbon::parse($servicio->fecha_final)->format('d/m/Y') : '--' }}
                </td>
                
                <td class="text-center bold">
                    @if($servicio->tipo_servicio === 'COMBUSTIBLE' && $servicio->cantidad_litros)
                        {{ number_format($servicio->cantidad_litros, 0) }} Lts.
                    @else
                        <span style="color: #cbd5e1;">--</span>
                    @endif
                </td>
                
                <td class="text-right bold">Bs. {{ number_format($servicio->monto, 2) }}</td>
                
                <td class="text-center bold">{{ $servicio->porcentaje_avance }}%</td>
                
                <td style="text-align: center;">
                    @php $tieneDocs = false; @endphp
                    
                    @if($servicio->convocatoria) <span class="doc-tag">CONV</span> @php $tieneDocs = true; @endphp @endif
                    @if($servicio->documento_base) <span class="doc-tag">DBC</span> @php $tieneDocs = true; @endphp @endif
                    @if($servicio->acta_apertura) <span class="doc-tag">ACTA</span> @php $tieneDocs = true; @endphp @endif
                    @if($servicio->resolucion_adjudicacion) <span class="doc-tag">RES</span> @php $tieneDocs = true; @endphp @endif
                    @if($servicio->informe) <span class="doc-tag">INF</span> @php $tieneDocs = true; @endphp @endif
                    
                    @if(!$tieneDocs)
                        <span style="color: #cbd5e1; font-style: italic;">Sin archivos</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Generado por: <strong>{{ auth()->user()?->responsable?->nombre_apellido ?? auth()->user()?->name ?? 'Sistema' }}</strong> 
        | Portal de Control de Activos y Contratos DDELPZ
    </div>

</body>
</html>