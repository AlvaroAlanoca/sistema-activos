<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ActaResource\Pages;
use App\Models\Acta;
use App\Models\Responsable;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ActaResource extends Resource
{
    protected static ?string $model = Acta::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-duplicate';
    protected static ?string $recordTitleAttribute = 'numero_acta';
    protected static ?string $navigationLabel = 'Gestión de Actas';
    protected static ?string $navigationGroup = 'Transacciones';

    public static function form(Form $form): Form
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        
        // Usamos load() para cargar todo el árbol de datos a la memoria de forma segura
        $user?->load(['responsable.oficinaCargo.oficina', 'responsable.oficinaCargo.cargo']);
        
        // Ahora simplemente le pasamos el objeto cargado a la variable
        $solicitante = $user?->responsable;

        return $form
            ->schema([
                // SECCIÓN 1: DATOS DEL FUNCIONARIO SOLICITANTE
                Forms\Components\Section::make('Datos Del Funcionario Solicitante')
                    ->schema([
                        Forms\Components\Grid::make(4) 
                            ->schema([
                                Forms\Components\Placeholder::make('sol_nombre')
                                    ->label('Apellidos y Nombres:')
                                    ->content($solicitante?->nombre_apellido ?? 'ADMINISTRADOR NO VINCULADO')
                                    ->extraAttributes(['class' => 'font-semibold text-primary-600']),
                                
                                Forms\Components\Placeholder::make('sol_ci')
                                    ->label('Nro. Documento:')
                                    ->content($solicitante?->ci ?? 'N/D'),
                                
                                Forms\Components\Placeholder::make('sol_item')
                                    ->label('Nro. Ítem:')
                                    ->content($solicitante?->numero_item ?? 'N/D')
                                    ->extraAttributes(['class' => 'text-info-600 font-medium']),
                                
                                Forms\Components\Placeholder::make('sol_oficina')
                                    ->label('Oficina:')
                                    ->content($solicitante?->oficinaCargo?->oficina?->descripcion ?? 'N/D'),

                                Forms\Components\Placeholder::make('sol_cargo')
                                    ->label('Cargo:')
                                    ->content($solicitante?->oficinaCargo?->cargo?->descripcion ?? 'N/D')
                                    ->columnSpan(4), 
                            ]),
                    ]),

                // SECCIÓN 2: DATOS DEL FUNCIONARIO RECEPTOR
                Forms\Components\Section::make('Datos Del Funcionario Receptor')
                    ->schema([
                        Forms\Components\Select::make('id_responsables')
                            ->label('Búsqueda Apellidos y Nombres')
                            ->relationship(
                                name: 'responsable', 
                                titleAttribute: 'nombre_apellido',
                                modifyQueryUsing: function (Builder $query) {
                                    
                                    // NUEVO FILTRO: Excluir administradores usando Spatie
                                    $adminIds = \App\Models\User::whereHas('roles', function($q) {
                                            $q->whereIn('name', ['admin', 'super_admin']);
                                        })
                                        ->whereNotNull('responsable_id')
                                        ->pluck('responsable_id')
                                        ->toArray();

                                    $miId = Auth::user()?->responsable_id;
                                    
                                    // Evitamos que el responsable se transfiera a sí mismo
                                    if ($miId && !in_array($miId, $adminIds)) {
                                        $adminIds[] = $miId;
                                    }

                                    if (!empty($adminIds)) {
                                        $query->whereNotIn('idresponsables', $adminIds);
                                    }
                                }
                            )
                            ->searchable()
                            ->preload()
                            ->required()
                            ->live(), 

                        Forms\Components\Grid::make(4) 
                            ->schema([
                                Forms\Components\Placeholder::make('rec_ci')
                                    ->label('Nro. Documento:')
                                    ->content(function (Get $get) {
                                        if (!$get('id_responsables')) return '--';
                                        return Responsable::find($get('id_responsables'))?->ci ?? 'N/D';
                                    }),
                                
                                Forms\Components\Placeholder::make('rec_item')
                                    ->label('Nro. Ítem:')
                                    ->content(function (Get $get) {
                                        if (!$get('id_responsables')) return '--';
                                        return Responsable::find($get('id_responsables'))?->numero_item ?? 'N/D';
                                    })
                                    ->extraAttributes(['class' => 'text-info-600 font-medium']),
                                
                                Forms\Components\Placeholder::make('rec_oficina')
                                    ->label('Oficina:')
                                    ->content(function (Get $get) {
                                        if (!$get('id_responsables')) return '--';
                                        $receptor = Responsable::with('oficinaCargo.oficina')->find($get('id_responsables'));
                                        return $receptor?->oficinaCargo?->oficina?->descripcion ?? 'N/D';
                                    }),

                                Forms\Components\Placeholder::make('rec_cargo')
                                    ->label('Cargo:')
                                    ->content(function (Get $get) {
                                        if (!$get('id_responsables')) return '--';
                                        $receptor = Responsable::with('oficinaCargo.cargo')->find($get('id_responsables'));
                                        return $receptor?->oficinaCargo?->cargo?->descripcion ?? 'N/D';
                                    })
                                    ->columnSpan(4), 
                            ]),
                    ]),

                // SECCIÓN 3: SOLICITUD DE TRANSFERENCIA
                Forms\Components\Section::make('Solicitud De Transferencia')
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\Hidden::make('tipo')->default('TRANSFERENCIA INTERNA'),
                        
                                Forms\Components\Placeholder::make('tipo_transf')
                                    ->label('Tipo Transferencia:')->content('TRANSFERENCIA INTERNA'),
                        
                                Forms\Components\TextInput::make('numero_acta')
                                    ->label('Número de Acta')
                                    ->default(function () {
                                        $añoActual = date('Y');
                                        
                                        $ultimaActa = Acta::where('tipo', 'TRANSFERENCIA INTERNA')
                                            ->where('numero_acta', 'like', "%-{$añoActual}")
                                            ->orderBy('created_at', 'desc')
                                            ->first();

                                        $siguienteNumero = 1;

                                        if ($ultimaActa && preg_match('/-(\d+)-/', $ultimaActa->numero_acta, $coincidencias)) {
                                            $siguienteNumero = (int) $coincidencias[1] + 1;
                                        }

                                        return 'TRANS-' . str_pad($siguienteNumero, 3, '0', STR_PAD_LEFT) . '-' . $añoActual;
                                    })
                                    ->readOnly() 
                                    ->dehydrated() 
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->extraInputAttributes(['style' => 'font-weight: bold; color: #0284c7; background-color: #f0f9ff;']),             
                                
                                Forms\Components\DatePicker::make('created_at')
                                    ->label('Fecha de la solicitud:')->default(now())
                                    ->displayFormat('d/m/Y')->disabled()->dehydrated(),
                            ]),
                        Forms\Components\Textarea::make('observaciones')->label('Observaciones')->rows(2)->columnSpan('full'),
                    ]),

                // SECCIÓN 4: ACTIVOS
                Forms\Components\Section::make('Seleccione Los Activos Que Desea Transferir')
                    ->schema([
                        Forms\Components\Repeater::make('items')
                            ->relationship('items')
                            ->schema([
                                Forms\Components\Select::make('id_bienes')
                                    ->label('Búsqueda (Código o Descripción)')
                                    ->relationship(
                                        name: 'bien', 
                                        titleAttribute: 'descripcion',
                                        modifyQueryUsing: function (Builder $query) {
                                            $query->where('estado', 'DISPONIBLE');
                                            
                                             /** @var \App\Models\User $user */
                                            $user = Auth::user();
                                            if ($user && $user->hasRole('responsable')) {
                                                // Asegúrate de que la columna se llame 'id_responsable' en la base de datos de bienes
                                                $query->where('id_responsable', $user->responsable_id);
                                            }
                                        }
                                    )
                                    ->searchable(['codigo', 'descripcion'])
                                    ->preload()
                                    ->getOptionLabelFromRecordUsing(fn ($record) => "[{$record->codigo}] - {$record->descripcion}")
                                    ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                                    ->required()->columnSpan(2),

                                Forms\Components\Select::make('estado')
                                    ->label('Estado Actual')
                                    ->options(['Bueno' => 'Bueno', 'Regular' => 'Regular', 'Malo' => 'Malo'])
                                    ->required()->columnSpan(1),
                            ])
                            ->columns(3)->addActionLabel('Agregar Activo')->reorderable(false)
                    ]),
            ]);
    }

    protected static function autollenarReceptor(Set $set, $state): void
    {
        if (!$state) {
            $set('rec_ci', '--'); $set('rec_item', '--'); $set('rec_oficina', '--'); $set('rec_cargo', '--');
            return;
        }

        $receptor = Responsable::with(['oficinaCargo.oficina', 'oficinaCargo.cargo'])->find($state);

        if ($receptor) {
            $set('rec_ci', $receptor->ci ?? 'N/D');
            $set('rec_item', $receptor->numero_item ?? 'N/D');
            $set('rec_oficina', $receptor->oficinaCargo?->oficina?->descripcion ?? 'N/D');
            $set('rec_cargo', $receptor->oficinaCargo?->cargo?->descripcion ?? 'N/D');
        }
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('numero_acta')
                    ->label('Nro. de Acta')
                    ->searchable() 
                    ->sortable()
                    ->weight('bold')
                    ->color('primary'),

                Tables\Columns\TextColumn::make('tipo')
                    ->label('Tipo de Acta')
                    ->badge() 
                    ->color(fn (string $state): string => match ($state) {
                        'DEVOLUCION' => 'danger',
                        'TRANSFERENCIA INTERNA' => 'warning',
                        default => 'success',
                    }),

                Tables\Columns\TextColumn::make('responsable.nombre_apellido')
                    ->label('Funcionario Involucrado')
                    ->searchable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha de Emisión')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                
                Tables\Actions\Action::make('imprimir')
                    ->label('Imprimir PDF')
                    ->icon('heroicon-o-printer')
                    ->color('danger')
                    ->button()
                    ->url(fn (Acta $record) => route('acta.imprimir', $record))
                    ->openUrlInNewTab(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    // NUEVO: Filtro automático de la tabla principal
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        // Si el usuario es un responsable (y no es admin), solo puede ver sus propias actas
        if ($user && $user->hasRole('responsable') && !$user->hasRole('super_admin')) {
            $query->where('id_responsables', $user->responsable_id);
        }
        return $query;
    }

    // Permisos base de Shield
    public static function getPermissionPrefixes(): array
    {
        return [
            'view',
            'view_any',
            'create',
            'update',
            'delete',
            'delete_any',
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListActas::route('/'),
            'create' => Pages\CreateActa::route('/create'),
            'edit' => Pages\EditActa::route('/{record}/edit'),
        ];
    }
}