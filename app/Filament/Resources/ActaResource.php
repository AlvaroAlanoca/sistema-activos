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
use Barryvdh\DomPDF\Facade\Pdf;
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
        $user = \Illuminate\Support\Facades\Auth::user();
        
        // Usamos load() para cargar todo el árbol de datos a la memoria de forma segura
        $user?->load(['responsable.oficinaCargo.oficina', 'responsable.oficinaCargo.cargo']);
        
        // Ahora simplemente le pasamos el objeto cargado a la variable
        $solicitante = $user?->responsable;

        return $form
            ->schema([
                // SECCIÓN 1: DATOS DEL FUNCIONARIO SOLICITANTE
                Forms\Components\Section::make('Datos Del Funcionario Solicitante')
                    ->schema([
                        Forms\Components\Grid::make(4) // Cambié a 4 para que quede simétrico
                            ->schema([
                                Forms\Components\Placeholder::make('sol_nombre')
                                    ->label('Apellidos y Nombres:')
                                    ->content($solicitante?->nombre_apellido ?? 'ADMINISTRADOR NO VINCULADO')
                                    ->extraAttributes(['class' => 'font-semibold text-primary-600']),
                                
                                Forms\Components\Placeholder::make('sol_ci')
                                    ->label('Nro. Documento:')
                                    ->content($solicitante?->ci ?? 'N/D'),
                                
                                // ELIMINAMOS sol_gerencia y CONFIGURAMOS sol_item
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
                                    ->columnSpan(4), // Que ocupe toda la fila inferior para que no se corte si es largo
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
                                modifyQueryUsing: function (\Illuminate\Database\Eloquent\Builder $query) {
                                    
                                    $adminIds = \App\Models\User::where('rol', 'admin')
                                        ->whereNotNull('responsable_id')
                                        ->pluck('responsable_id')
                                        ->toArray();

                                    $miId = \Illuminate\Support\Facades\Auth::user()?->responsable_id;
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
                            ->live(), // Hace que se recarguen los datos de abajo al elegir a la persona

                        Forms\Components\Grid::make(4) // Cambié a 4 columnas
                            ->schema([
                                Forms\Components\Placeholder::make('rec_ci')
                                    ->label('Nro. Documento:')
                                    ->content(function (Get $get) {
                                        if (!$get('id_responsables')) return '--';
                                        return Responsable::find($get('id_responsables'))?->ci ?? 'N/D';
                                    }),
                                
                                // NUEVO: Extraemos el número de ítem del receptor de forma dinámica
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
                                    ->columnSpan(4), // Igual que arriba, que el cargo tenga su propio espacio si es largo
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
                            
                            // 1. Buscamos usando el guion en lugar de la barra
                            $ultimaActa = \App\Models\Acta::where('tipo', 'TRANSFERENCIA INTERNA')
                                ->where('numero_acta', 'like', "%-{$añoActual}")
                                ->orderBy('created_at', 'desc')
                                ->first();

                            $siguienteNumero = 1;

                            // 2. Ajustamos la expresión regular para buscar entre dos guiones (Ej: TRANS-001-2026)
                            if ($ultimaActa && preg_match('/-(\d+)-/', $ultimaActa->numero_acta, $coincidencias)) {
                                $siguienteNumero = (int) $coincidencias[1] + 1;
                            }

                            // 3. Armamos el formato final usando un guion antes del año
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
                                        modifyQueryUsing: fn (Builder $query) => $query->where('estado', 'DISPONIBLE')
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

    // Actualizado para limpiar y llenar la variable correcta (rec_item en vez de rec_gerencia)
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
                    ->url(fn (\App\Models\Acta $record) => route('acta.imprimir', $record))
                    ->openUrlInNewTab(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        if ($user && $user->rol === 'responsable') {
            $query->where('id_responsables', $user->responsable_id);
        }
        return $query;
    }

    public static function canViewAny(): bool
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        return $user && $user->rol === 'admin';
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