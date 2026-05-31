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
            // 👇 Personalizamos el botón superior que lleva al formulario
            Actions\CreateAction::make()
                ->label('Dar baja Activo')
                ->icon('heroicon-o-minus-circle'), // Un ícono representativo de "dar de baja"
        ];
    }
}