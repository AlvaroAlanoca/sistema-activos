<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\ServicioGasolina;
use App\Models\Servicio;
use Illuminate\Support\Facades\Auth;
use App\Models\Acta;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Aquí es donde puedes registrar las rutas web para tu aplicación.
|
*/
Route::get('/', function () {
    return redirect('/admin');
});

Route::get('/acta/{acta}/imprimir', function (App\Models\Acta $acta) {
    
    // 1. Cargamos el acta asegurándonos de traer todo su "árbol" de relaciones
    $acta->load([
        'responsable.oficinaCargo.oficina', 
        'responsable.oficinaCargo.cargo', 
        'items.bien'
    ]);

    // 2. Cargamos al Administrador (el usuario que tiene la sesión iniciada)
    $adminResponsable = null;
    
    /** @var \App\Models\User|null $user */
    $user = Illuminate\Support\Facades\Auth::user();

    if ($user) {
        $adminResponsable = $user->responsable()->with(['oficinaCargo.oficina', 'oficinaCargo.cargo'])->first();
    }

    // 3. Asignamos quién es quién dependiendo de la operación
    if ($acta->tipo === 'DEVOLUCION') {

        $entregador = $acta->responsable;
        $receptor = $adminResponsable;
    } else {
        $entregador = $adminResponsable;
        $receptor = $acta->responsable;
    }

    // 4. Renderizamos la vista PDF inyectándole las variables exactas
    $pdf = Pdf::loadView('pdf.acta', [
        'acta' => $acta,
        'entregador' => $entregador, // Usar esta variable en el Blade para "Entregué Conforme"
        'receptor' => $receptor,     // Usar esta variable en el Blade para "Recibí Conforme"
        'items' => $acta->items,
        

        'solicitante' => $entregador, 
    ]);

    // 5. Mostramos el PDF en una nueva pestaña
    $nombreSeguro = str_replace('/', '-', $acta->numero_acta);

    return $pdf->stream("Acta_{$nombreSeguro}.pdf");

})->name('acta.imprimir')->middleware('auth');
Route::get('/servicios/imprimir', function () {
    
    // 1. Traemos todos los servicios ordenados por los más recientes
    $servicios = \App\Models\Servicio::orderBy('created_at', 'desc')->get();

    // 2. Renderizamos la vista del PDF (en formato horizontal)
    // Nota: Usaré 'pdf.servicios' para mantener el orden de tu carpeta de vistas
    $pdf = Pdf::loadView('pdf.servicios', [
        'servicios' => $servicios,
        'fecha_generacion' => now()->format('d/m/Y H:i')
    ])->setPaper('letter', 'landscape');

    // 3. Mostramos el PDF
    return $pdf->stream("Reporte_Servicios_DDELPZ.pdf");


    
})->name('servicios.imprimir')->middleware('auth');
    Route::get('/solicitud/{solicitud}/imprimir', function (App\Models\Solicitud $solicitud) {
    
    // 1. Cargamos las relaciones para evitar consultas N+1 en la vista PDF
    $solicitud->load([
        'responsable.oficinaCargo.oficina', 
        'responsable.oficinaCargo.cargo', 
        'bien.tipoBien'
    ]);

    // 2. Definimos quién es el solicitante
    $solicitante = $solicitud->responsable;

    // 3. Formateamos un número de control (Ej: SOL-00015-2026)
    $numeroSolicitud = 'SOL-' . str_pad($solicitud->id, 5, '0', STR_PAD_LEFT) . '-' . $solicitud->created_at->format('Y');

    // 4. Renderizamos la vista PDF
    $pdf = Pdf::loadView('pdf.solicitud', [
        'solicitud' => $solicitud,
        'solicitante' => $solicitante,
        'numero_solicitud' => $numeroSolicitud,
    ]);

    // 5. Mostramos el PDF
    return $pdf->stream("Comprobante_{$numeroSolicitud}.pdf");

})->name('solicitud.imprimir')->middleware('auth');
Route::get('/reporte-gasolina', function (Request $request) {
    
    // 1. CAPTURA DE PARÁMETROS DE FILTRADO
    $desde = $request->query('desde', now()->startOfMonth()->toDateString());
    $hasta = $request->query('hasta', now()->toDateString());
    $placasSeleccionadas = $request->query('placas', []);

    // 2. CONSULTA DE VALES CONSUMIDOS POR VEHÍCULO
    $query = ServicioGasolina::select(
            'id_servicio',
            'placa',
            DB::raw('COUNT(idservicio_gasolina) as total_cargas'),
            DB::raw('SUM(cantidad_litros) as total_litros')
        )
        ->whereBetween('fecha_vale', [$desde, $hasta]);

    // Aplicar filtro condicional si el usuario seleccionó placas específicas en Filament
    if (!empty($placasSeleccionadas)) {
        $query->whereIn('placa', $placasSeleccionadas);
    }

    $resumenPlacas = $query->groupBy('id_servicio', 'placa')
        ->orderBy('total_litros', 'desc')
        ->get();

    // Sumatoria del volumen total despachado en el periodo para el pie de página
    $granTotalLitros = $resumenPlacas->sum('total_litros');

    // 3. CÁLCULO DE SALDOS DE CONTRATOS USANDO LA LLAVE 'idservicios'
    $idsServiciosUsados = $resumenPlacas->pluck('id_servicio')->unique();
    
    $resumenContratos = Servicio::whereIn('idservicios', $idsServiciosUsados)->get()->map(function($servicio) {
        
        // Sumamos todo el consumo histórico de este contrato para calcular el saldo real al vuelo
        $consumoHistorico = ServicioGasolina::where('id_servicio', $servicio->idservicios)->sum('cantidad_litros');
        
        return (object) [
            'empresa' => $servicio->empresa,
            'cuce' => $servicio->cuce,
            'total_contrato' => $servicio->cantidad_litros,
            'saldo_actual' => $servicio->cantidad_litros - $consumoHistorico
        ];
    });

    // 4. GENERACIÓN Y RENDERIZADO DEL PDF
    $pdf = Pdf::loadView('pdf.gasolina_resumen', [
        'resumen' => $resumenPlacas,
        'contratos' => $resumenContratos,
        'desde' => Carbon::parse($desde)->format('d/m/Y'),
        'hasta' => Carbon::parse($hasta)->format('d/m/Y'),
        'granTotal' => $granTotalLitros,
        'placasFiltro' => empty($placasSeleccionadas) ? 'TODAS LAS PLACAS' : implode(', ', $placasSeleccionadas),
    ]);

    // Envía el stream para que el navegador lo abra directamente en la pestaña nueva
    return $pdf->stream("Reporte_Combustible_Filtrado.pdf");

})->name('gasolina.reporte')->middleware('auth');