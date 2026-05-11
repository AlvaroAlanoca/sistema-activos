<?php

namespace App\Filament\Resources;

use App\Models\Acta;
use App\Models\ActaItem;
use App\Models\Bien;
use Filament\Notifications\Notification;
use App\Filament\Resources\ResponsableResource\Pages;
use App\Models\Responsable;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class ResponsableResource extends Resource
{
    protected static ?string $model = Responsable::class;
    
    // Configuración de Menú
    protected static ?string $navigationGroup = 'Administración de Personal';
    protected static ?int $navigationSort = 1;
    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    
    // Nombres limpios
    protected static ?string $modelLabel = 'Responsable / Funcionario';
    protected static ?string $pluralModelLabel = 'Funcionarios Responsables';

    public static function form(Form $form): Form
    {
        return $form->schema([
            
            \Filament\Forms\Components\Section::make('Datos Personales del Funcionario')
                ->description('Ingrese la información básica y de identificación del responsable.')
                ->icon('heroicon-o-user')
                ->schema([
                    Forms\Components\TextInput::make('nombre_apellido')
                        ->label('Apellidos y Nombres')
                        ->placeholder('Ej: Perez Lopez Juan Carlos')
                        ->required()
                        ->maxLength(255),

                    Forms\Components\TextInput::make('ci')
                        ->label('Carnet de Identidad (C.I.)')
                        ->required()
                        ->maxLength(20)
                        ->unique(ignoreRecord: true), 
                ])->columns(2),

            \Filament\Forms\Components\Section::make('Ubicación y Cargo')
                ->description('Determine el número de ítem, dónde trabaja y qué cargo ocupa en la institución.')
                ->icon('heroicon-o-briefcase')
                ->schema([
                    // NUEVO CAMPO: Número de Ítem reemplaza a Gerencia
                    Forms\Components\TextInput::make('numero_item')
                        ->label('Número de Ítem')
                        ->placeholder('Ej: 1234') 
                        ->required()
                        ->maxLength(50),

                Forms\Components\Select::make('id_oficinas_cargos')
                    ->label('Oficina y Cargo Exacto')
                    ->options(function () {

                    return \App\Models\OficinaCargo::with(['oficina', 'cargo'])
                    ->get()

                    ->sortBy(fn ($record) => $record->oficina->descripcion)

                    ->mapWithKeys(fn ($record) => [
                    $record->idoficinas_cargos => "{$record->oficina->descripcion} - {$record->cargo->descripcion}"
            ]);
    })
    ->searchable()
    ->preload()
    ->required(),
                ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nombre_apellido')
                    ->label('Nombres y Apellidos')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('ci')
                    ->label('C.I.')
                    ->searchable(),

                // NUEVA COLUMNA EN TABLA: Mostramos el número de ítem
                Tables\Columns\TextColumn::make('numero_item')
                    ->label('Nro. Ítem')
                    ->searchable()
                    ->sortable()
                    ->badge() // Lo ponemos como etiqueta para diferenciarlo de los números de carnet
                    ->color('info'),

                Tables\Columns\TextColumn::make('oficinaCargo.oficina.descripcion')
                    ->label('Oficina')
                    ->searchable()
                    ->sortable()
                    ->wrap(),
                Tables\Columns\TextColumn::make('oficinaCargo.cargo.descripcion')
                    ->label('Cargo')
                    ->searchable()
                    ->sortable()
                    ->wrap(),

                Tables\Columns\TextColumn::make('bienes_asignados')
                    ->label('Estado de Activos')
                    ->getStateUsing(function (\App\Models\Responsable $record) {
                        $misItems = \App\Models\ActaItem::with('bien')
                            ->whereHas('acta', function ($query) use ($record) {
                                $query->where('id_responsables', $record->getKey());
                            })
                            ->whereHas('bien', function ($query) {
                                $query->where('estado', 'ENTREGADO');
                            })
                            ->get();

                        $bienesReales = 0;

                        foreach ($misItems as $item) {
                            $ultimoId = \App\Models\ActaItem::where('id_bienes', $item->id_bienes)->max('idacta_items');
                            if ($item->idacta_items === $ultimoId) {
                                $bienesReales++;
                            }
                        }

                        return $bienesReales;
                    })
                    ->badge() 
                    ->sortable()
                    ->color(fn (int $state): string => $state > 0 ? 'success' : 'gray')
                    ->formatStateUsing(fn (int $state): string => $state > 0 ? "Tiene {$state} activo(s)" : 'Sin activos asignados'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                
                Tables\Actions\Action::make('recibir_devolucion')
                    ->label('Recibir Devolución')
                    ->icon('heroicon-o-clipboard-document-check')
                    ->color('danger')
                    ->modalHeading(fn ($record) => 'Devolución de Activos - ' . $record->nombre_apellido)
                    ->modalWidth('2xl')
                    ->form(function ($record) {
                        
                        $misItems = \App\Models\ActaItem::with('bien')
                            ->whereHas('acta', function ($query) use ($record) {
                                $query->where('id_responsables', $record->getKey());
                            })
                            ->whereHas('bien', function ($query) {
                                $query->where('estado', 'ENTREGADO');
                            })
                            ->get();

                        $bienesAsignados = [];

                        foreach ($misItems as $item) {
                            $ultimoId = \App\Models\ActaItem::where('id_bienes', $item->id_bienes)->max('idacta_items');
                            if ($item->idacta_items === $ultimoId) {
                                $bienesAsignados[$item->bien->idbienes] = "[{$item->bien->codigo}] - {$item->bien->descripcion}";
                            }
                        }

                        if (empty($bienesAsignados)) {
                            return [
                                \Filament\Forms\Components\Placeholder::make('sin_bienes')
                                    ->label('')
                                    ->content('✅ Este funcionario no tiene bienes pendientes por devolver.')
                                    ->extraAttributes(['style' => 'color: green; font-weight: bold; text-align: center;']),
                            ];
                        }

                        return [
                            \Filament\Forms\Components\CheckboxList::make('bienes_a_devolver')
                                ->label('Seleccione los equipos que está entregando físicamente:')
                                ->options($bienesAsignados)
                                ->bulkToggleable()
                                ->columns(1)
                                ->required(),

                            \Filament\Forms\Components\Textarea::make('observaciones')
                                ->label('Observaciones / Estado del equipo al devolver')
                                ->placeholder('Ej: Monitor con rayones, teclado funcional...')
                                ->required(),
                        ];
                    })
                    ->action(function ($record, array $data) {
                        
                        if (empty($data['bienes_a_devolver'])) {
                            return; 
                        }

                        $nuevaActa = Acta::create([
                            'tipo' => 'DEVOLUCION',
                            'numero_acta' => 'DEV-' . now()->format('Ymd-His'),
                            'id_responsables' => $record->idresponsables,
                            'observaciones' => $data['observaciones'],
                        ]);

                        foreach ($data['bienes_a_devolver'] as $idBien) {
                            ActaItem::create([
                                'id_acta' => $nuevaActa->idacta,
                                'id_bienes' => $idBien,
                                'estado' => 'Devuelto', 
                            ]);

                            Bien::where('idbienes', $idBien)->update(['estado' => 'DISPONIBLE']);
                        }

                        Notification::make()
                            ->success()
                            ->title('Devolución Procesada')
                            ->body('Los bienes seleccionados han regresado al estado DISPONIBLE.')
                            ->actions([
                                \Filament\Notifications\Actions\Action::make('imprimir')
                                    ->label('Imprimir Comprobante')
                                    ->button()
                                    ->color('danger')
                                    ->url(route('acta.imprimir', $nuevaActa), shouldOpenInNewTab: true),
                            ])
                            ->send();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('nombre_apellido', 'asc'); // Siempre es bueno ordenar por nombre por defecto
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