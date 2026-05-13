<?php

namespace App\Filament\Pages\Auth;

use Filament\Pages\Auth\Login as BaseLogin;
use Illuminate\Contracts\Support\Htmlable;

class CustomLogin extends BaseLogin
{
    // Cambiamos el título principal
    public function getHeading(): string|Htmlable
    {
        return 'CONTROL DE BIENES Y SERVICIOS DDELPZ';
    }
public function getTitle(): string | \Illuminate\Contracts\Support\Htmlable{
    return '';
}
    // Agregamos un subtítulo profesional
    public function getSubheading(): string|Htmlable|null
    {
        return 'Ingrese sus credenciales institucionales';
    }
}