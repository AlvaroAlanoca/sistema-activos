<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SolicitudResource\Pages;
use App\Filament\Resources\SolicitudResource\RelationManagers;
use App\Models\Solicitud;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SolicitudResource extends Resource
{
    protected static ?string $model = Solicitud::class;
    protected static ?string $navigationGroup = 'Gestión de Inventario';
    protected static ?int $navigationSort = 4;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
        protected static ?string $modelLabel = 'Solicitudes';
    protected static ?string $pluralModelLabel = 'Solicitudes';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

// Asegúrate de importar los Forms y Tables arriba
public static function table(Table $table): Table
{
    return $table
        ->columns([
            Tables\Columns\TextColumn::make('bien.codigo')->label('Código Activo')->weight('bold'),
            Tables\Columns\TextColumn::make('bien.descripcion')->label('Equipo Solicitado')->limit(30),
            Tables\Columns\TextColumn::make('responsable.nombre_apellido')->label('Solicitante'),
            Tables\Columns\TextColumn::make('estado')
                ->badge()
                ->color(fn (string $state): string => match ($state) {
                    'PENDIENTE' => 'warning',
                    'APROBADA' => 'success',
                    'RECHAZADA' => 'danger',
                }),
            Tables\Columns\TextColumn::make('created_at')->label('Fecha')->date(),
        ])
        ->defaultSort('created_at', 'desc')
        ->actions([
Tables\Actions\Action::make('aprobar')
    ->label('Aprobar y Generar Acta')
    ->icon('heroicon-o-check-circle')
    ->color('success')
    ->visible(fn ($record) => $record->estado === 'PENDIENTE')
    ->action(function ($record) {
        // 1. Cambiamos los estados en la base de datos
        $record->update(['estado' => 'APROBADA']);
        $record->bien->update(['estado' => 'DISPONIBLE']); 
        
        // 2. Mostramos la alerta
        \Filament\Notifications\Notification::make()
            ->success()
            ->title('Aprobada')
            ->body('Solicitud aprobada. Proceda a formalizar el Acta de Entrega.')
            ->send();

        // 3. REDIRECCIÓN INTERNA: Esto fuerza a que el código de arriba se ejecute primero
        return redirect(\App\Filament\Resources\ActaResource::getUrl('create', [
            'responsable_id' => $record->responsable_id,
            'bien_id' => $record->bien_id,
        ]));
    }),

            Tables\Actions\Action::make('rechazar')
                ->label('Rechazar')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->visible(fn ($record) => $record->estado === 'PENDIENTE')
                ->form([
                    \Filament\Forms\Components\Textarea::make('respuesta_admin')
                        ->label('Motivo del Rechazo')
                        ->required(),
                ])
                ->action(function ($record, array $data) {
                    $record->update([
                        'estado' => 'RECHAZADA',
                        'respuesta_admin' => $data['respuesta_admin'],
                    ]);
                    // Devolver el bien al catálogo
                    $record->bien->update(['estado' => 'DISPONIBLE']); 
                }),
                Tables\Actions\Action::make('imprimir')
        ->label('Imprimir Comprobante')
        ->icon('heroicon-o-printer')
        ->color('danger')
        ->button()
        // Redirige a la misma ruta de impresión pasando el registro de la solicitud
        ->url(fn (\App\Models\Solicitud $record) => route('solicitud.imprimir', $record))
        ->openUrlInNewTab(),
        ]);
}

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSolicituds::route('/'),

            //'edit' => Pages\EditSolicitud::route('/{record}/edit'),
        ];
    }
}
