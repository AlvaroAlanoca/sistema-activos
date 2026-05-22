<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class AccionesAccesoDirecto extends Widget
{
    protected static string $view = 'filament.widgets.acciones-acceso-directo';

    // 1. Obliga al widget a ocupar todo el ancho disponible (salto de línea)
    protected int | string | array $columnSpan = 'full';

    // 2. Define el orden de aparición (asegura que vaya después de la bienvenida)
    protected static ?int $sort = -1;
}