<?php

namespace App\Filament\Resources\OficinaCargoResource\Pages;

use App\Filament\Resources\OficinaCargoResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateOficinaCargo extends CreateRecord
{
    protected static string $resource = OficinaCargoResource::class;
        protected function getFormActions(): array
    {
        return [
            $this->getCreateFormAction(), // Botón de Crear principal
            $this->getCancelFormAction(), // Botón de Cancelar
            // Al no incluir getCreateAnotherFormAction(), el botón desaparece
        ];
    }
    protected function getCreatedNotification(): ?\Filament\Notifications\Notification
    {
    return \Filament\Notifications\Notification::make()
        ->success()
        ->title('Registro Exitoso')
        ->body('Los datos se han guardado correctamente en el sistema.');
    }
}
