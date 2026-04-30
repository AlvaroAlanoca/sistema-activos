<?php
use App\Models\Acta;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Route;

// Ruta protegida para generar el PDF desde una URL
Route::get('/imprimir-acta/{record}', function (Acta $record) {
    $pdf = Pdf::loadView('pdf.acta', [
        'acta' => $record,
        'items' => $record->items()->with('bien')->get(),
    ]);
    
    // El método stream() abre el visor de impresión del navegador
    return $pdf->stream("Acta_{$record->numero_acta}.pdf");
})->name('acta.imprimir')->middleware(['auth']);