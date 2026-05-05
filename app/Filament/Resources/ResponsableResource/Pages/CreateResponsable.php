<?php

namespace App\Filament\Resources\ResponsableResource\Pages;

use App\Filament\Resources\ResponsableResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateResponsable extends CreateRecord
{
    protected static string $resource = ResponsableResource::class;
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
