<?php

namespace App\Filament\Resources\ServicioResource\Pages;

use App\Filament\Resources\ServicioResource;
use Filament\Resources\Pages\CreateRecord;

class CreateServicio extends CreateRecord
{
    protected static string $resource = ServicioResource::class;

    //1. Vaciamos la memoria de Livewire justo después de guardar en MySQL
    protected function afterCreate(): void
    {
        $camposBlob = ['convocatoria', 'documento_base', 'acta_apertura', 'resolucion_adjudicacion', 'informe'];
        
        foreach ($camposBlob as $campo) {
            if (isset($this->data[$campo])) {
                $this->data[$campo] = null;
            }
        }
    }

    //2. Forzamos una redirección automática a la tabla principal
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}