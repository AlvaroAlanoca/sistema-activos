<?php

namespace App\Filament\Resources\CargoResource\Pages;

use App\Filament\Resources\CargoResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateCargo extends CreateRecord
{
    protected static string $resource = CargoResource::class;
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
