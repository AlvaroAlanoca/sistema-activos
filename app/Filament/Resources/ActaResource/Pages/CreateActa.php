<?php

namespace App\Filament\Resources\ActaResource\Pages;

use App\Filament\Resources\ActaResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateActa extends CreateRecord
{
    protected static string $resource = ActaResource::class;
    
    protected function getFormActions(): array
    {
        return [
            $this->getCreateFormAction()
                ->label('Guardar e Imprimir')
                ->requiresConfirmation() // Esto añade el mensaje de confirmación antes de ejecutar
                ->modalHeading('Confirmar Entrega')
                ->modalDescription('¿Desea finalizar el registro de esta acta y generar el PDF?')
                ->modalSubmitActionLabel('Sí, confirmar'),
            $this->getCancelFormAction(),
        ];
    }
    
    protected function getCreatedNotification(): ?\Filament\Notifications\Notification
    {
        return \Filament\Notifications\Notification::make()
            ->success()
            ->title('Acta Registrada')
            ->body('El acta de entrega se ha generado e impreso correctamente.');
    }
    
    protected function afterCreate(): void
    {
        // 1. LA MAGIA NUEVA: Cambiar el estado de los equipos a ENTREGADO
        $acta = $this->record;
        
        foreach ($acta->items as $item) {
            \App\Models\Bien::where('idbienes', $item->id_bienes)
                ->update(['estado' => 'ENTREGADO']);
        }

        // 2. TU LÓGICA ORIGINAL: Imprimir el PDF en una nueva pestaña
        $this->js("window.open('" . route('acta.imprimir', $this->record) . "', '_blank');");
    }
    
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}