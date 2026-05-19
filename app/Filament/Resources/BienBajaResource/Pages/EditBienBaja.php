<?php

namespace App\Filament\Resources\BienBajaResource\Pages;

use App\Filament\Resources\BienBajaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBienBaja extends EditRecord
{
    protected static string $resource = BienBajaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //Actions\DeleteAction::make(),
        ];
    }
}
