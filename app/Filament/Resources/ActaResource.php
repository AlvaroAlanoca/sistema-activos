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
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\Placeholder::make('sol_nombre')
                                    ->label('Apellidos y Nombres:')
                                    ->content($solicitante?->nombre_apellido ?? 'ADMINISTRADOR NO VINCULADO')
                                    ->extraAttributes(['class' => 'font-semibold text-primary-600']),
                                
                                Forms\Components\Placeholder::make('sol_ci')
                                    ->label('Nro. Documento de Identidad:')
                                    ->content($solicitante?->ci ?? 'N/D'),
                                
                                Forms\Components\Placeholder::make('sol_gerencia')
                                    ->label('Gerencia:')
                                    ->content($solicitante?->gerencia ?? 'N/D'),
                                
                                Forms\Components\Placeholder::make('sol_oficina')
                                    ->label('Oficina:')
                                    // Viaje de 3 pasos basado en tu esquema ER
                                    ->content($solicitante?->oficinaCargo?->oficina?->descripcion ?? 'N/D'),

                                Forms\Components\Placeholder::make('sol_cargo')
                                    ->label('Cargo:')
                                    // Viaje de 3 pasos basado en tu esquema ER
                                    ->content($solicitante?->oficinaCargo?->cargo?->descripcion ?? 'N/D'),
                                
                                Forms\Components\Placeholder::make('sol_item')
                                    ->label('Item:')
                                    ->content('N/D'),
                            ]),
                    ]),

                // SECCIÓN 2: DATOS DEL FUNCIONARIO RECEPTOR
                Forms\Components\Section::make('Datos Del Funcionario Receptor')
                    ->schema([
                        Forms\Components\Select::make('id_responsables')
                            ->label('Búsqueda Apellidos y Nombres')
                            // Usamos modifyQueryUsing para filtrar la relación en tiempo real
                            ->relationship(
                                name: 'responsable', 
                                titleAttribute: 'nombre_apellido',
                                modifyQueryUsing: function (\Illuminate\Database\Eloquent\Builder $query) {
                                    // 1. Obtenemos el ID de responsable del usuario actual (el Admin)
                                    $miId = Auth::user()?->responsable_id;
                                    
                                    // 2. Si existe, lo excluimos de la lista
                                    if ($miId) {
                                        $query->where('idresponsables', '!=', $miId);
                                    }
                                }
                            )
                            ->searchable()
                            ->preload()
                            ->required()
                            ->live(), 

                        Forms\Components\Grid::make(3)
                            ->schema([
                                
                                Forms\Components\Placeholder::make('rec_ci')
                                    ->label('Nro. Documento de Identidad:')
                                    ->content(function (Get $get) {
                                        if (!$get('id_responsables')) return '--';
                                        return Responsable::find($get('id_responsables'))?->ci ?? 'N/D';
                                    }),
                                
                                Forms\Components\Placeholder::make('rec_gerencia')
                                    ->label('Gerencia:')
                                    ->content(function (Get $get) {
                                        if (!$get('id_responsables')) return '--';
                                        return Responsable::find($get('id_responsables'))?->gerencia ?? 'N/D';
                                    }),
                                
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
                                    }),
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
                                    ->label('Número de Acta')->placeholder('Ej: TRANS-001/2026')
                                    ->required()->unique(ignoreRecord: true),
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
                                    ->getOptionLabelFromRecordUsing(fn ($record) => "[{$record->codigo}] - {$record->descripcion}")
                                    ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                                    ->required()->columnSpan(2),

                                Forms\Components\Select::make('estado')
                                    ->label('Estado Actual')
                                    ->options(['Bueno' => 'Bueno', 'Regular' => 'Regular', 'Malo' => 'Malo'])
                                    ->required()->columnSpan(1),
                            ])
                            ->columns(3)->addActionLabel('Agregar Activo')->reorderable(false)
                            ->itemLabel(fn (array $state): ?string => $state['id_bienes'] ?? 'Nuevo Ítem'),
                    ]),
            ]);
    }

    protected static function autollenarReceptor(Set $set, $state): void
    {
        if (!$state) {
            $set('rec_ci', '--'); $set('rec_gerencia', '--'); $set('rec_oficina', '--'); $set('rec_cargo', '--');
            return;
        }

        // Cargamos al responsable seleccionado incluyendo las sub-relaciones
        $receptor = Responsable::with(['oficinaCargo.oficina', 'oficinaCargo.cargo'])->find($state);

        if ($receptor) {
            $set('rec_ci', $receptor->ci ?? 'N/D');
            $set('rec_gerencia', $receptor->gerencia ?? 'N/D');
            // Navegamos por el diagrama ER exacto
            $set('rec_oficina', $receptor->oficinaCargo?->oficina?->descripcion ?? 'N/D');
            $set('rec_cargo', $receptor->oficinaCargo?->cargo?->descripcion ?? 'N/D');
        }
    }

public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // 1. LA COLUMNA DE BÚSQUEDA
                Tables\Columns\TextColumn::make('numero_acta')
                    ->label('Nro. de Acta')
                    ->searchable() 
                    ->sortable()
                    ->weight('bold')
                    ->color('primary'),

                Tables\Columns\TextColumn::make('tipo')
                    ->label('Tipo de Acta')
                    ->badge() // Lo hace ver como una etiqueta de color
                    ->color(fn (string $state): string => match ($state) {
                        'DEVOLUCION' => 'danger',
                        'TRANSFERENCIA INTERNA' => 'warning',
                        default => 'success',
                    }),

                Tables\Columns\TextColumn::make('responsable.nombre_apellido')
                    ->label('Funcionario Involucrado')
                    ->searchable(), // También podrás buscar por nombre de la persona

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha de Emisión')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                // Aquí puedes agregar filtros por fecha si lo deseas en el futuro
            ])
            ->actions([
                // Solo dejamos el botón de ver, NO el de editar (las actas legales no se editan)
                Tables\Actions\ViewAction::make(),
                
                // 2. EL BOTÓN DE IMPRIMIR RE-EDICIÓN
                Tables\Actions\Action::make('imprimir')
                    ->label('Imprimir PDF')
                    ->icon('heroicon-o-printer')
                    ->color('danger')
                    ->button() // Lo hace ver como un botón sólido

                    ->url(fn (\App\Models\Acta $record) => route('acta.imprimir', $record))
                    ->openUrlInNewTab(), // Que no cierre el sistema al imprimir
            ])
            ->defaultSort('created_at', 'desc'); // Muestra las más recientes primero
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
    // Esta función decide si la pestaña aparece en el menú lateral
    public static function canViewAny(): bool
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        
        // Solo retorna "true" (mostrar) si el usuario es administrador
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