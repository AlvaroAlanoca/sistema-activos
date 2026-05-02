<?php

namespace App\Filament\Pages\Auth;

use Filament\Pages\Auth\Login as BaseLogin;
use Illuminate\Contracts\Support\Htmlable;

class CustomLogin extends BaseLogin
{
    // Cambiamos el título principal
    public function getHeading(): string|Htmlable
    {
        return 'Sistema de Activos Fijos';
    }

    // Agregamos un subtítulo profesional
    public function getSubheading(): string|Htmlable|null
    {
        return 'SEDUCA - Ingrese sus credenciales institucionales';
    }
}