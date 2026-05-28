<?php

namespace App\Filament\Resources\ServicioResource\Pages;

use App\Filament\Resources\ServicioResource;
use Filament\Resources\Pages\EditRecord;

class EditServicio extends EditRecord
{
    protected static string $resource = ServicioResource::class;

    // 1. Vaciamos la memoria después de actualizar la base de datos
    protected function afterSave(): void
    {
        $camposBlob = ['convocatoria', 'documento_base', 'acta_apertura', 'resolucion_adjudicacion', 'informe'];
        
        foreach ($camposBlob as $campo) {
            if (isset($this->data[$campo])) {
                $this->data[$campo] = null;
            }
        }
    }

    // 👇 2. Forzamos una redirección automática a la tabla principal
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}