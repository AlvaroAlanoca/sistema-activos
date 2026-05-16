<?php

namespace App\Filament\Resources\ServicioContratoResource\Pages;

use App\Filament\Resources\ServicioContratoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListServicioContratos extends ListRecords
{
    protected static string $resource = ServicioContratoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
