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
                ->extraAttributes([
        'class' => 'rounded-full hover:scale-105 transition-transform shadow-lg',
        'style' => 'background-color: #10B981 !important;' // Un verde más vibrante
    ])
                ->url(fn () => route('servicios.imprimir'), shouldOpenInNewTab: true),

            // BOTÓN ORIGINAL
            Actions\CreateAction::make(),
        ];
    }
}