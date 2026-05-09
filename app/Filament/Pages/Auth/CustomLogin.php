<?php

namespace App\Filament\Pages\Auth;

use Filament\Pages\Auth\Login as BaseLogin;
use Illuminate\Contracts\Support\Htmlable;

class CustomLogin extends BaseLogin
{
    // Cambiamos el título principal
    public function getHeading(): string|Htmlable
    {
        return 'SISTEMA DE CONTROL DE BIENES Y SERVICIOS';
    }

    // Agregamos un subtítulo profesional
    public function getSubheading(): string|Htmlable|null
    {
        return 'Ingrese sus credenciales institucionales';
    }
}