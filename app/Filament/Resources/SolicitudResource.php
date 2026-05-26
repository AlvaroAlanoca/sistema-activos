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
            Tables\Columns\TextColumn::make('estado')
    ->badge()
    ->color(fn (string $state): string => match ($state) {
        'PENDIENTE' => 'warning',  // Amarillo (Esperando al Aceptador)
        'APROBADA' => 'info',      // Azul (Aprobado, esperando al Admin)
        'COMPLETADA' => 'success', // Verde (Acta generada, todo listo)
        'RECHAZADA' => 'danger',   // Rojo
        default => 'gray',
    }),
        ])
        ->defaultSort('created_at', 'desc')
        ->actions([
    
    // =========================================================================
    // ACCIÓN 1: SOLO PARA EL "ACEPTADOR" (Autoriza la solicitud)
    // =========================================================================
    Tables\Actions\Action::make('autorizar')
        ->label('Autorizar Petición')
        ->icon('heroicon-o-check-badge')
        ->color('success')
        ->visible(function ($record) {
            /** @var \App\Models\User|null $user */
            $user = \Illuminate\Support\Facades\Auth::user();
            // Solo visible si está PENDIENTE y el usuario tiene el rol de aceptador
            return $record->estado === 'PENDIENTE' && $user && $user->hasRole('aceptador'); 
        })
        ->requiresConfirmation()
        ->modalHeading('Autorizar Solicitud')
        ->modalDescription('¿Confirma que el Administrador puede proceder a entregar este activo?')
        ->action(function ($record) {
            // Solo cambia el estado, NO genera el acta ni libera el bien todavía
            $record->update(['estado' => 'APROBADA']);
            
            \Filament\Notifications\Notification::make()
                ->success()
                ->title('Petición Autorizada')
                ->body('El Administrador ha sido notificado para generar el Acta.')
                ->send();
        }),

    // =========================================================================
    // ACCIÓN 2: SOLO PARA EL "ACEPTADOR" (Rechaza la solicitud)
    // =========================================================================
    Tables\Actions\Action::make('rechazar')
        ->label('Rechazar')
        ->icon('heroicon-o-x-circle')
        ->color('danger')
        ->visible(function ($record) {
            /** @var \App\Models\User|null $user */
            $user = \Illuminate\Support\Facades\Auth::user();
            return $record->estado === 'PENDIENTE' && $user && $user->hasRole('aceptador');
        })
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
            // Como se rechazó, devolvemos el bien al catálogo disponible
            $record->bien->update(['estado' => 'DISPONIBLE']); 
        }),

    // =========================================================================
    // ACCIÓN 3: SOLO PARA EL "ADMINISTRADOR" (Genera el Acta)
    // =========================================================================
    Tables\Actions\Action::make('generar_acta')
        ->label('Generar Acta de Entrega')
        ->icon('heroicon-o-document-plus')
        ->color('info') // Azul para diferenciarlo de la autorización
        ->visible(function ($record) {
            /** @var \App\Models\User|null $user */
            $user = \Illuminate\Support\Facades\Auth::user();
            // Solo visible si ya fue APROBADA por el jefe y el usuario es admin
            return $record->estado === 'APROBADA' && $user && $user->hasAnyRole(['admin', 'super_admin']);
        })
        ->action(function ($record) {
            // 1. Marcamos la solicitud como COMPLETADA para que ya no estorbe en la bandeja
            $record->update(['estado' => 'COMPLETADA']);
            
            // 2. Liberamos el bien para que el formulario del Acta pueda seleccionarlo
            $record->bien->update(['estado' => 'DISPONIBLE']); 
            
            // 3. Redirigimos al formulario mágico con los datos precargados
            return redirect(\App\Filament\Resources\ActaResource::getUrl('create', [
                'responsable_id' => $record->responsable_id,
                'bien_id' => $record->bien_id,
            ]));
        }),

    // Acción para imprimir el PDF del comprobante (Visible para todos)
    Tables\Actions\Action::make('imprimir')
        ->label('Ver Comprobante')
        ->icon('heroicon-o-printer')
        ->color('gray')
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
