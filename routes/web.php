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

// Opcional: Redirigir la raíz del sitio directamente al panel de inicio de sesión de SEDUCA

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

    // 3. LA MAGIA: Asignamos quién es quién dependiendo de la operación
    if ($acta->tipo === 'DEVOLUCION') {
        // Si devuelven algo: El funcionario entrega, el admin recibe.
        $entregador = $acta->responsable;
        $receptor = $adminResponsable;
    } else {
        // Si es transferencia: El admin entrega, el funcionario recibe.
        $entregador = $adminResponsable;
        $receptor = $acta->responsable;
    }

    // 4. Renderizamos la vista PDF inyectándole las variables exactas
    $pdf = Pdf::loadView('pdf.acta', [
        'acta' => $acta,
        'entregador' => $entregador, // Usar esta variable en el Blade para "Entregué Conforme"
        'receptor' => $receptor,     // Usar esta variable en el Blade para "Recibí Conforme"
        'items' => $acta->items,
        
        // Salvavidas: Por si tu diseño en Blade todavía usa $solicitante en alguna línea
        'solicitante' => $entregador, 
    ]);

    // 5. Mostramos el PDF en una nueva pestaña
    // Limpiamos el nombre para que sea seguro para Windows/Linux
    $nombreSeguro = str_replace('/', '-', $acta->numero_acta);

    // Cambié la palabra "Transferencia" por "Acta" para que sea genérico (sirva para Devoluciones también)
    return $pdf->stream("Acta_{$nombreSeguro}.pdf");

})->name('acta.imprimir')->middleware('auth');