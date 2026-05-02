<?php

namespace App\Filament\Widgets;
use Illuminate\Support\Facades\Auth;

use Filament\Widgets\Widget;

class BotonesResponsableWidget extends Widget
{
    protected static string $view = 'filament.widgets.botones-responsable-widget';

    // Esto hace que el widget ocupe todo el ancho de la pantalla
    protected int | string | array $columnSpan = 'full';

    // Este widget SOLO lo ve el responsable
    public static function canView(): bool
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        
        return $user && $user->rol === 'responsable';
    }
}