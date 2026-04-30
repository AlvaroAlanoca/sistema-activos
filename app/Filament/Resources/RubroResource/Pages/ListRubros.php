<?php

namespace App\Filament\Resources\RubroResource\Pages;

use App\Filament\Resources\RubroResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRubros extends ListRecords
{
    protected static string $resource = RubroResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
