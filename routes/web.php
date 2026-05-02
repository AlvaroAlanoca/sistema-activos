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

    // 2. Cargamos al solicitante (de forma segura para que el editor no marque error)
    $solicitante = null;
    
    /** @var \App\Models\User|null $user */
    $user = Illuminate\Support\Facades\Auth::user();

    if ($user) {
        $solicitante = $user->responsable()->with(['oficinaCargo.oficina', 'oficinaCargo.cargo'])->first();
    }

    // 3. Renderizamos la vista PDF inyectándole las variables exactas
$pdf = Pdf::loadView('pdf.acta', [
        'acta' => $acta,
        'receptor' => $acta->responsable,
        'solicitante' => $solicitante, // <-- Si falta esta línea, da el error que mencionas
        'items' => $acta->items,
    ]);

    // 4. Mostramos el PDF en una nueva pestaña
    return $pdf->stream("Transferencia_{$acta->numero_acta}.pdf");

})->name('acta.imprimir')->middleware('auth');