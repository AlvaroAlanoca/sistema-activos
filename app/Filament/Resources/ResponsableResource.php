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
            
            \Filament\Forms\Components\Section::make('Datos Personales del Funcionario')
                ->description('Ingrese la información básica del responsable.')
                ->schema([
                    Forms\Components\TextInput::make('nombre_apellido')
                        ->label('Apellidos y Nombres')
                        ->required()
                        ->maxLength(255),

                    Forms\Components\TextInput::make('ci')
                        ->label('Carnet de Identidad (C.I.)')
                        ->required()
                        ->maxLength(20)
                        // ¡TRUCO PRO! Esto evita que registres el mismo CI dos veces, 
                        // pero te permite editar al usuario sin que lance error.
                        ->unique(ignoreRecord: true), 
                ])->columns(2), // Pone estos dos campos en 2 columnas (uno al lado del otro)

            \Filament\Forms\Components\Section::make('Ubicación y Cargo')
                ->description('Determine dónde trabaja y qué cargo ocupa en la institución.')
                ->schema([
                    Forms\Components\TextInput::make('gerencia')
                        ->label('Gerencia a la que pertenece')
                        ->placeholder('Ej: Gerencia Administrativa Financiera')
                        ->required()
                        ->maxLength(255),

                    // Relacionamos con la tabla pivote de oficinas_cargos
                    Forms\Components\Select::make('id_oficinas_cargos')
                        ->label('Oficina y Cargo Exacto')
                        ->relationship('oficinaCargo', 'idoficinas_cargos') 
                        ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->oficina->descripcion} - {$record->cargo->descripcion}")
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

                // Agregamos el C.I.
                Tables\Columns\TextColumn::make('ci')
                    ->label('C.I.')
                    ->searchable(),

                // Mostramos la descripción del Cargo (Navegando por la relación)
                Tables\Columns\TextColumn::make('oficinaCargo.cargo.descripcion')
                    ->label('Cargo')
                    ->searchable()
                    ->sortable()
                    ->wrap(), // Permite que el texto baje a la siguiente línea si es muy largo

                // NUEVA COLUMNA: Indicador de Bienes Asignados

// NUEVA COLUMNA: Indicador de Bienes Asignados (Lógica Infalible PHP+DB)
                Tables\Columns\TextColumn::make('bienes_asignados')
                    ->label('Estado de Activos')
                    ->getStateUsing(function (\App\Models\Responsable $record) {
                        
                        // 1. Traemos todo el historial de este usuario con equipos que siguen "ENTREGADOS"
                        $misItems = \App\Models\ActaItem::with('bien')
                            ->whereHas('acta', function ($query) use ($record) {
                                $query->where('id_responsables', $record->getKey());
                            })
                            ->whereHas('bien', function ($query) {
                                $query->where('estado', 'ENTREGADO');
                            })
                            ->get();

                        $bienesReales = 0;

                        // 2. Filtramos uno por uno preguntando si es el ÚLTIMO movimiento absoluto
                        foreach ($misItems as $item) {
                            $ultimoId = \App\Models\ActaItem::where('id_bienes', $item->id_bienes)->max('idacta_items');
                            
                            // Si mi registro coincide con el último movimiento del equipo, soy el dueño actual
                            if ($item->idacta_items === $ultimoId) {
                                $bienesReales++;
                            }
                        }

                        return $bienesReales;
                    })
                    ->badge() 
                    ->color(fn (int $state): string => $state > 0 ? 'success' : 'gray')
                    ->formatStateUsing(fn (int $state): string => $state > 0 ? "Tiene {$state} activo(s)" : 'Sin activos asignados'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                
                // NUEVO BOTÓN: Devolución Específica con Casillas
                Tables\Actions\Action::make('recibir_devolucion')
                    ->label('Recibir Devolución')
                    ->icon('heroicon-o-clipboard-document-check')
                    ->color('danger')
                    ->modalHeading(fn ($record) => 'Devolución de Activos - ' . $record->nombre_apellido)
                    ->modalWidth('2xl')
                    ->form(function ($record) {
                        
// 1. Buscamos el historial de bienes de esta persona
                        $misItems = \App\Models\ActaItem::with('bien')
                            ->whereHas('acta', function ($query) use ($record) {
                                $query->where('id_responsables', $record->getKey());
                            })
                            ->whereHas('bien', function ($query) {
                                $query->where('estado', 'ENTREGADO');
                            })
                            ->get();

                        $bienesAsignados = [];

                        // 2. Armamos la lista de casillas SOLO con los que realmente posee hoy
                        foreach ($misItems as $item) {
                            $ultimoId = \App\Models\ActaItem::where('id_bienes', $item->id_bienes)->max('idacta_items');
                            
                            if ($item->idacta_items === $ultimoId) {
                                $bienesAsignados[$item->bien->idbienes] = "[{$item->bien->codigo}] - {$item->bien->descripcion}";
                            }
                        }

                        // 2. Si no tiene nada, mostramos un mensaje verde
                        if (empty($bienesAsignados)) {
                            return [
                                \Filament\Forms\Components\Placeholder::make('sin_bienes')
                                    ->label('')
                                    ->content('✅ Este funcionario no tiene bienes pendientes por devolver.')
                                    ->extraAttributes(['style' => 'color: green; font-weight: bold; text-align: center;']),
                            ];
                        }

                        // 3. Si tiene bienes, mostramos las casillas
                        return [
                            \Filament\Forms\Components\CheckboxList::make('bienes_a_devolver')
                                ->label('Seleccione los equipos que está entregando físicamente:')
                                ->options($bienesAsignados)
                                ->bulkToggleable() // Permite seleccionar todos con 1 clic
                                ->columns(1)
                                ->required(),

                            \Filament\Forms\Components\Textarea::make('observaciones')
                                ->label('Observaciones / Estado del equipo al devolver')
                                ->placeholder('Ej: Monitor con rayones, teclado funcional...')
                                ->required(),
                        ];
                    })
                    ->action(function ($record, array $data) {
                        
                        // Si no seleccionó nada o no había bienes, detenemos la acción
                        if (empty($data['bienes_a_devolver'])) {
                            return; 
                        }

                        // 1. Creamos la nueva Acta de Devolución
                        $nuevaActa = Acta::create([
                            'tipo' => 'DEVOLUCION',
                            'numero_acta' => 'DEV-' . now()->format('Ymd-His'),
                            'id_responsables' => $record->idresponsables,
                            'observaciones' => $data['observaciones'],
                        ]);

                        // 2. Registramos los ítems y los volvemos "DISPONIBLES"
                        foreach ($data['bienes_a_devolver'] as $idBien) {
                            
                            ActaItem::create([
                                'id_acta' => $nuevaActa->idacta,
                                'id_bienes' => $idBien,
                                'estado' => 'Devuelto', // Puedes poner 'Bueno', 'Malo', etc.
                            ]);

                            // Actualizamos tu tabla de bienes para que vuelvan al almacén
                            Bien::where('idbienes', $idBien)->update(['estado' => 'DISPONIBLE']);
                        }

                        // 3. Notificación y Botón de Imprimir
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
            ]);
    }
    public static function canViewAny(): bool
{
    /** @var \App\Models\User|null $user */
    $user = \Illuminate\Support\Facades\Auth::user();
    
    // Solo devuelve 'true' (visible) si el rol es admin
    return $user && $user->rol === 'admin';
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
