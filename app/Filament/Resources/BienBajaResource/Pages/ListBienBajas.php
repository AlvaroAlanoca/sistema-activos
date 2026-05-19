<?php

namespace App\Filament\Resources\BienBajaResource\Pages;

use App\Filament\Resources\BienBajaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBienBajas extends ListRecords
{
    protected static string $resource = BienBajaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
