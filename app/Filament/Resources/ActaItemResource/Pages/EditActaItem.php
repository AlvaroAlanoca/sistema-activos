<?php

namespace App\Filament\Resources\ActaItemResource\Pages;

use App\Filament\Resources\ActaItemResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditActaItem extends EditRecord
{
    protected static string $resource = ActaItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
