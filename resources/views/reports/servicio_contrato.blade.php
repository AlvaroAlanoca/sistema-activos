<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: 'Helvetica', sans-serif; color: #333; font-size: 14px; }
        
        /* Estilos del Encabezado */
        .header-table { width: 100%; border-collapse: collapse; border-bottom: 2px solid #004a99; margin-bottom: 20px; }
        .header-logo { width: 20%; text-align: left; padding-bottom: 10px; }
        .header-text { width: 80%; text-align: right; padding-bottom: 10px; vertical-align: middle; }
        .header-text h1 { margin: 0; font-size: 24px; color: #004a99; font-weight: bold; }
        .header-text .subtitle { margin: 5px 0 0 0; font-size: 16px; color: #555; letter-spacing: 1px; }
        
        /* Estilos de la Tabla de Datos */
        .info-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .info-table td { padding: 10px; border: 1px solid #ddd; }
        .label { font-weight: bold; background-color: #f2f2f2; width: 30%; }
        
        /* Estilos de Descripción y Firma */
        .description-box { margin-top: 30px; }
        .description-title { color: #004a99; border-bottom: 1px solid #ddd; padding-bottom: 5px; font-size: 16px; }
        .description-content { padding: 15px; background-color: #fafafa; border: 1px solid #eee; min-height: 120px; line-height: 1.5; }
        
        .footer { margin-top: 60px; text-align: center; font-size: 12px; color: #777; }
        .signature-container { margin-top: 80px; width: 100%; text-align: center; }
        .signature-line { display: inline-block; width: 250px; border-top: 1px solid #333; padding-top: 5px; }
    </style>
</head>
<body>

    <table class="header-table">
        <tr>
            <td class="header-logo">
                <img src="{{ public_path('img/logo.png') }}" alt="Logo" style="height: 70px; width: auto;">
            </td>
            <td class="header-text">
                <h1>DIRECCIÓN DEPARTAMENTAL DE EDUCACIÓN LA PAZ DDELPZ - BOLIVIA</h1>
                <p class="subtitle">REPORTE DE CONTRATACIÓN DE SERVICIOS</p>
            </td>
        </tr>
    </table>

    <table class="info-table">
        <tr>
            <td class="label">Código de Contrato:</td>
            <td>#{{ $contrato->idservicio_contrato }}</td>
        </tr>
        <tr>
            <td class="label">CUCE:</td>
            <td>{{ $contrato->servicio->cuce }}</td>
        </tr>
                <tr>
            <td class="label">Descripción:</td>
            <td>{{ $contrato->servicio->descripcion }}</td>
        </tr>
        <tr>
            <td class="label">Empresa Proveedora:</td>
            <td>{{ $contrato->servicio->empresa }}</td>
        </tr>
        <tr>
            <td class="label">Responsable a Cargo:</td>
            <td>{{ $contrato->user->responsable->nombre_apellido ?? 'Usuario sin ficha' }}</td>
        </tr>
        <tr>
            <td class="label">Vigencia:</td>
            <td>Desde: {{ \Carbon\Carbon::parse($contrato->fecha_inicio)->format('d/m/Y') }} - Hasta: {{ \Carbon\Carbon::parse($contrato->fecha_fin)->format('d/m/Y') }}</td>
        </tr>
        <tr>
            <td class="label">Estado Actual:</td>
            <td style="text-transform: uppercase; font-weight: bold; color: {{ $contrato->estado === 'cumplido' ? '#10b981' : '#f59e0b' }};">
                {{ $contrato->estado }}
            </td>
        </tr>
    </table>

    <div class="description-box">
        <h3 class="description-title">DESCRIPCIÓN Y ACTIVIDADES</h3>
        <div class="description-content">
            {{ $contrato->descripcion ?? 'Sin descripción registrada.' }}
        </div>
    </div>

    <div class="footer">
<p>
            Generado por: <strong>{{ auth()->user()?->responsable?->nombre_apellido ?? auth()->user()?->name ?? 'Sistema' }}</strong> 
            | Sistema DDELPZ - {{ now()->format('d/m/Y H:i') }}
        </p>
        <div class="signature-container">
            <br><br>
            <span class="signature-line">Firma Responsable</span>
        </div>
    </div>

</body>
</html>