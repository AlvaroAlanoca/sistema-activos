<?php

namespace App\Filament\Resources\OficinaResource\Pages;

use App\Filament\Resources\OficinaResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateOficina extends CreateRecord
{
    protected static string $resource = OficinaResource::class;
        protected function getFormActions(): array
    {
        return [
            $this->getCreateFormAction(), // Botón de Crear principal
            $this->getCreateAnotherFormAction(),
            $this->getCancelFormAction(), // Botón de Cancelar

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
