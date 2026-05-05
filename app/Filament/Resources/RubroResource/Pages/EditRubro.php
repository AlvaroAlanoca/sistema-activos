<?php

namespace App\Filament\Resources\RubroResource\Pages;

use App\Filament\Resources\RubroResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRubro extends EditRecord
{
    protected static string $resource = RubroResource::class;

    protected function getHeaderActions(): array
    {
        return [
          
        ];
    }
}
