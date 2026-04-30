<?php

namespace App\Filament\Resources\ActaResource\Pages;

use App\Filament\Resources\ActaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListActas extends ListRecords
{
    protected static string $resource = ActaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
