<?php

namespace App\Filament\Resources\ActaResource\Pages;

use App\Filament\Resources\ActaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditActa extends EditRecord
{
    protected static string $resource = ActaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
