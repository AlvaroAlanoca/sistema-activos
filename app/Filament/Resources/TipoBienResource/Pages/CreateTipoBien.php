<?php

namespace App\Filament\Resources\TipoBienResource\Pages;

use App\Filament\Resources\TipoBienResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateTipoBien extends CreateRecord
{
    protected static string $resource = TipoBienResource::class;
//Si quieres que al darle "Crear" te mande directo a la lista
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
            protected function getFormActions(): array
    {
        return [
            $this->getCreateFormAction(),
            $this->getCreateAnotherFormAction(),
            $this->getCancelFormAction(),
             
        ];
    }
}