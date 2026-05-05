<?php

namespace App\Filament\Resources\OficinaCargoResource\Pages;

use App\Filament\Resources\OficinaCargoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditOficinaCargo extends EditRecord
{
    protected static string $resource = OficinaCargoResource::class;

    protected function getHeaderActions(): array
    {
        return [
           
        ];
    }
}
