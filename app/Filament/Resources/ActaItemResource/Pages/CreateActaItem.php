<?php

namespace App\Filament\Resources\ActaItemResource\Pages;

use App\Filament\Resources\ActaItemResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateActaItem extends CreateRecord
{
    protected static string $resource = ActaItemResource::class;
    protected function getFormActions(): array
    {
        return [
            $this->getCreateFormAction(), // Botón de Crear principal
            $this->getCancelFormAction(), // Botón de Cancelar
            // Al no incluir getCreateAnotherFormAction(), el botón desaparece
        ];
    }
}
