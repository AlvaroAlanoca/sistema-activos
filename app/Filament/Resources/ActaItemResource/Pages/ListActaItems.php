<?php

namespace App\Filament\Resources\ActaItemResource\Pages;

use App\Filament\Resources\ActaItemResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListActaItems extends ListRecords
{
    protected static string $resource = ActaItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
