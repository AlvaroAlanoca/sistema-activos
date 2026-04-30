<?php

namespace App\Filament\Resources;
use App\Models\Acta;
use App\Models\ActaItem;
use App\Models\Bien;
use Filament\Notifications\Notification;
use Tables\Actions\Action;
use App\Filament\Resources\ResponsableResource\Pages;
use App\Filament\Resources\ResponsableResource\RelationManagers;
use App\Models\Responsable;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ResponsableResource extends Resource
{
    protected static ?string $model = Responsable::class;
    protected static ?string $navigationGroup = 'Administración de Personal';
    protected static ?int $navigationSort = 1;
    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    public static function form(Form $form): Form
    {
    return $form->schema([
            Forms\Components\TextInput::make('nombre_apellido')
                ->required()
                ->maxLength(255),
            
            // Relacionamos con la tabla pivote de oficinas_cargos
            Forms\Components\Select::make('id_oficinas_cargos')
                ->label('Oficina y Cargo')
                ->relationship('oficinaCargo', 'idoficinas_cargos') // Asegúrate de tener esta relación en el modelo Responsable
                ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->oficina->descripcion} - {$record->cargo->descripcion}")
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
            Tables\Columns\TextColumn::make('nombre_apellido')->searchable(),
            Tables\Columns\TextColumn::make('id_oficinas_cargos')->label('Asignación'),
        ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('devolucion_total')
                ->label('Devolver Todo')
                ->icon('heroicon-o-arrow-path')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Procesar Devolución Masiva')
                ->modalDescription('¿Está seguro de que desea registrar la devolución de TODOS los bienes asignados a esta persona?')
                ->action(function ($record) {
                    // 1. Buscamos los items entregados a este responsable a través de sus actas
                    $itemsPendientes = ActaItem::whereHas('acta', function ($query) use ($record) {
                        $query->where('id_responsables', $record->idresponsables);
                    })->whereHas('bien', function ($query) {
                        $query->where('estado', 'ENTREGADO');
                    })->get();

                    if ($itemsPendientes->isEmpty()) {
                        Notification::make()
                            ->title('Sin bienes pendientes')
                            ->warning()
                            ->send();
                        return;
                    }

                    // 2. Creamos el Acta de Devolución automáticamente
                    $nuevaActa = Acta::create([
                        'tipo' => 'DEVOLUCION',
                        'numero_acta' => 'DEV-' . now()->format('Ymd-His'),
                        'id_responsables' => $record->idresponsables,
                    ]);

                    // 3. Procesamos cada bien
                    foreach ($itemsPendientes as $item) {
                        // Creamos el registro en la nueva acta
                        ActaItem::create([
                            'id_acta' => $nuevaActa->idacta,
                            'id_bienes' => $item->id_bienes,
                            'estado' => 'Bueno', // O podrías pedir el estado en un modal
                        ]);

                        // Liberamos el bien
                        Bien::where('idbienes', $item->id_bienes)
                            ->update(['estado' => 'DISPONIBLE']);
                    }

                    Notification::make()
                        ->title('Devolución procesada con éxito')
                        ->body("Se liberaron {$itemsPendientes->count()} bienes.")
                        ->success()
                        ->send();
                }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListResponsables::route('/'),
            'create' => Pages\CreateResponsable::route('/create'),
            'edit' => Pages\EditResponsable::route('/{record}/edit'),
        ];
    }
}
