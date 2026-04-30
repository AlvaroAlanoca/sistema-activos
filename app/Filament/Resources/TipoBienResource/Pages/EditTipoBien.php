<?php

namespace App\Filament\Resources\TipoBienResource\Pages;

use App\Filament\Resources\TipoBienResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTipoBien extends EditRecord
{
    protected static string $resource = TipoBienResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
