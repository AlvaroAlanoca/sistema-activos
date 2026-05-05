<?php

namespace App\Filament\Resources\BienResource\Pages;

use App\Filament\Resources\BienResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateBien extends CreateRecord
{
    protected static string $resource = BienResource::class;
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
