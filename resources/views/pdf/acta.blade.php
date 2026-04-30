<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Acta de {{ $acta->tipo }} - {{ $acta->numero_acta }}</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 11pt;
            margin: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header h1 {
            margin: 0;
            font-size: 18pt;
            text-transform: uppercase;
        }
        .info-section {
            margin-bottom: 20px;
        }
        .tabla-bienes {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .tabla-bienes th, .tabla-bienes td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }
        .tabla-bienes th {
            background-color: #f2f2f2;
        }

        /* ESTILOS DE LA TABLA DE FIRMAS (SIN BORDES) */
        .tabla-firmas {
            width: 100%;
            margin-top: 80px;
            border-collapse: collapse;
            border: none;
        }
        .tabla-firmas td {
            width: 50%;
            text-align: center;
            vertical-align: top;
            border: none;
            padding: 0 40px;
        }
        .linea {
            border-top: 1px solid #000;
            margin-top: 60px; /* Espacio para firma manual */
            padding-top: 8px;
        }
        .cargo {
            font-size: 10pt;
            color: #333;
            display: block;
            margin-top: 2px;
        }
        .footer-date {
            text-align: right;
            font-size: 9pt;
            margin-top: 20px;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>Acta de {{ $acta->tipo }} de Activos Fijos</h1>
        <p><strong>Número:</strong> {{ $acta->numero_acta }}</p>
    </div>

    <div class="info-section">
<p><strong>Fecha:</strong> {{ $acta->created_at ? $acta->created_at->format('d/m/Y H:i') : 'Sin fecha' }}</p>
<p><strong>Fecha:</strong> {{ optional($acta->created_at)->format('d/m/Y H:i') ?? now()->format('d/m/Y H:i') }}</p>
        <p><strong>Ubicación/Cargo:</strong> {{ $acta->responsable->oficinaCargo->nombre ?? 'La Paz - Bolivia' }}</p>
    </div>

    <table class="tabla-bienes">
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
                <td>{{ $item->bien->codigo }}</td>
                <td>{{ $item->bien->descripcion }}</td>
                <td>{{ $item->estado ?? 'Bueno' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <table class="tabla-firmas">
        <tr>
            <td>
                <strong>Entregué Conforme</strong>
                <div class="linea">
                    @if($acta->tipo == 'ENTREGA')
                        <strong>{{ auth()->user()->name }}</strong><br>
                        <span class="cargo">Encargado de Activos / Sistema</span>
                    @else
                        <strong>{{ $acta->responsable->nombre_apellido }}</strong><br>
                        <span class="cargo">Funcionario Responsable</span>
                    @endif
                </div>
            </td>

            <td>
                <strong>Recibí Conforme</strong>
                <div class="linea">
                    @if($acta->tipo == 'ENTREGA')
                        <strong>{{ $acta->responsable->nombre_apellido }}</strong><br>
                        <span class="cargo">Funcionario Responsable</span>
                    @else
                        <strong>{{ auth()->user()->name }}</strong><br>
                        <span class="cargo">Encargado de Activos / Sistema</span>
                    @endif
                </div>
            </td>
        </tr>
    </table>

    <div class="footer-date">
        <p>Documento generado por el Sistema de Control de Activos - {{ date('Y') }}</p>
    </div>

</body>
</html>