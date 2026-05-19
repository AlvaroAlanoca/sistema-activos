<?php

namespace App\Filament\Resources\ServicioResource\Pages;

use App\Filament\Resources\ServicioResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListServicios extends ListRecords
{
    protected static string $resource = ServicioResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // NUEVO BOTÓN DE REPORTE (Alineado a la izquierda del botón Crear)
            \Filament\Actions\Action::make('imprimir_reporte')
                ->label('Reporte')
                ->icon('heroicon-o-printer')
                ->color('success')
                ->url(fn () => route('servicios.imprimir'), shouldOpenInNewTab: true),

            // BOTÓN ORIGINAL
            Actions\CreateAction::make(),
        ];
    }
}