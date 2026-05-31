<?php

namespace App\Filament\Resources\BienBajaResource\Pages;

use App\Filament\Resources\BienBajaResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Actions\Action;

class CreateBienBaja extends CreateRecord
{
    protected static string $resource = BienBajaResource::class;

    protected function getCreateFormAction(): Action
    {
        return parent::getCreateFormAction()
            ->label('Guardar e Imprimir')
            ->icon('heroicon-o-printer');
    }


    protected function getCreateAnotherFormAction(): Action
    {
        return parent::getCreateAnotherFormAction()->hidden();
    }

    protected function afterCreate(): void
    {
        $baja = $this->record;
        $url = route('bien.baja.acta', ['id' => $baja->getKey()]);
        
        $this->js("window.open('{$url}', '_blank');");
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}