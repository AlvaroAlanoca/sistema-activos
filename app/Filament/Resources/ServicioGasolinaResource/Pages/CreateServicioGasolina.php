<?php

namespace App\Filament\Resources\ServicioGasolinaResource\Pages;

use App\Filament\Resources\ServicioGasolinaResource;
use Filament\Resources\Pages\CreateRecord;

class CreateServicioGasolina extends CreateRecord
{
    protected static string $resource = ServicioGasolinaResource::class;

    /**
     * 👇 LA MAGIA: Se ejecuta inmediatamente DESPUÉS de guardar el vale en la BD
     */
    protected function afterCreate(): void
    {
        // 1. Obtenemos el registro del vale recién creado con su ID
        $vale = $this->record;

        // 2. Construimos la URL de la ruta que generará este PDF individual
        $url = route('gasolina.acta.individual', ['id' => $vale->idservicio_gasolina]);

        // 3. Inyectamos JavaScript mediante Livewire para forzar la apertura en una nueva pestaña
        $this->js("window.open('{$url}', '_blank');");
    }

    /**
     * Define a dónde regresa la pantalla principal del sistema
     */
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}