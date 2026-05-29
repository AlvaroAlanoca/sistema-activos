<?php

namespace App\Filament\Resources\ServicioGasolinaResource\Pages;

use App\Filament\Resources\ServicioGasolinaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListServicioGasolinas extends ListRecords
{
    protected static string $resource = ServicioGasolinaResource::class;

    protected function getHeaderActions(): array
    {
        return [
           // Actions\CreateAction::make(),
        ];
    }
}
