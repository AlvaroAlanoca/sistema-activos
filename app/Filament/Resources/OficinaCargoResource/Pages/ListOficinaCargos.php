<?php

namespace App\Filament\Resources\OficinaCargoResource\Pages;

use App\Filament\Resources\OficinaCargoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListOficinaCargos extends ListRecords
{
    protected static string $resource = OficinaCargoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
