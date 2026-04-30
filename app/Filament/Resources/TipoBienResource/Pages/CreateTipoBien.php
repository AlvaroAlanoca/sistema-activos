<?php

namespace App\Filament\Resources\TipoBienResource\Pages;

use App\Filament\Resources\TipoBienResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateTipoBien extends CreateRecord
{
    protected static string $resource = TipoBienResource::class;
    protected function getCreatedNotification(): ?\Filament\Notifications\Notification
    {
    return \Filament\Notifications\Notification::make()
        ->success()
        ->title('Registro Exitoso')
        ->body('Los datos se han guardado correctamente en el sistema.');
    }

}
