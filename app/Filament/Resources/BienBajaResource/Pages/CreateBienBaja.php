<?php

namespace App\Filament\Resources\BienBajaResource\Pages;

use App\Filament\Resources\BienBajaResource;
use Filament\Resources\Pages\CreateRecord;

class CreateBienBaja extends CreateRecord
{
    protected static string $resource = BienBajaResource::class;

    // Automatización: Cambiar el estado del bien original
    protected function afterCreate(): void
    {
        $baja = $this->record;

        // Si el bien existe, le cambiamos el estado en la tabla principal
        if ($baja->bien) {
            $baja->bien->update([
                'estado' => 'DE BAJA' // Asegúrate de agregar 'DE BAJA' a las opciones de tu select de estados en BienResource
            ]);
        }
    }
}