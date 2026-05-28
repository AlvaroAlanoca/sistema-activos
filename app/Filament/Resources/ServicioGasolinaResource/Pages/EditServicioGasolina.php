<?php

namespace App\Filament\Resources\ServicioGasolinaResource\Pages;

use App\Filament\Resources\ServicioGasolinaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditServicioGasolina extends EditRecord
{
    protected static string $resource = ServicioGasolinaResource::class;

    protected function getHeaderActions(): array
    {
        return [
           // Actions\DeleteAction::make(),
        ];
    }
}
