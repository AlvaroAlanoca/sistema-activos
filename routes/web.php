<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Models\Acta;
use Barryvdh\DomPDF\Facade\Pdf;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Aquí es donde puedes registrar las rutas web para tu aplicación.
|
*/


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
