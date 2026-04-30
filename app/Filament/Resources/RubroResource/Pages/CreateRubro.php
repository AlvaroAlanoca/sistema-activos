<?php

namespace App\Filament\Resources\RubroResource\Pages;

use App\Filament\Resources\RubroResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateRubro extends CreateRecord
{
    protected static string $resource = RubroResource::class;
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
