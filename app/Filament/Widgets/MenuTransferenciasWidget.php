<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;
class MenuTransferenciasWidget extends Widget
{
    protected static string $view = 'filament.widgets.menu-transferencias-widget';
    // Esta función oculta el widget si el usuario no es admin
    public static function canView(): bool
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        
        return $user && $user->rol === 'admin';
    }
    // Hacemos que la tarjeta ocupe todo el ancho disponible o la mitad, según prefieras
    protected int | string | array $columnSpan = 'full'; 
}
