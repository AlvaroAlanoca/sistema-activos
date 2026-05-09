<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BienResource\Pages;
use App\Models\Bien;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get; // IMPORTANTE: Para la lógica reactiva
use Filament\Forms\Set; // IMPORTANTE: Para la lógica reactiva
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class BienResource extends Resource
{
    protected static ?string $model = Bien::class;
    
    protected static ?string $navigationGroup = 'Gestión de Inventario';
    protected static ?int $navigationSort = 1;
    protected static ?string $navigationIcon = 'heroicon-o-archive-box';

    protected static ?string $modelLabel = 'Bien / Activo';
    protected static ?string $pluralModelLabel = 'Bienes y Activos';

    public static function getNavigationLabel(): string
    {
        return Auth::user()?->rol === 'responsable' ? 'Mis Bienes Asignados' : 'Gestión de Bienes';
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            
            // SECCIÓN REACTIVA: CLASIFICACIÓN Y CODIFICACIÓN
            \Filament\Forms\Components\Section::make('Clasificación y Codificación')
                ->description('Asigne el tipo de bien y correlativo. El código patrimonial se generará automáticamente.')
                ->icon('heroicon-o-qr-code')
                ->schema([
                    Forms\Components\Select::make('id_tipo_bien')
                        ->label('Tipo de Bien')
                        ->relationship('tipoBien', 'descripcion')
                        ->searchable() 
                        ->preload()    
                        ->required()
                        ->live() // Hace el campo reactivo
                        ->afterStateUpdated(function (Set $set, Get $get, $state) {
                            $tipoBien = \App\Models\TipoBien::with('rubro')->find($state);
                            $codigoRubro = $tipoBien ? $tipoBien->rubro->codigo_rubro : '';
                            $correlativo = $get('correlativo') ?? '';
                            
                            $set('codigo', '266' . $codigoRubro . $correlativo);
                        }),

                    Forms\Components\TextInput::make('correlativo')
                        ->label('Correlativo')
                        ->numeric()
                        ->required()
                        ->live(debounce: 500) // Reacciona al terminar de teclear
                        ->afterStateUpdated(function (Set $set, Get $get, $state) {
                            $idTipoBien = $get('id_tipo_bien');
                            $codigoRubro = '';
                            
                            if ($idTipoBien) {
                                $tipoBien = \App\Models\TipoBien::with('rubro')->find($idTipoBien);
                                $codigoRubro = $tipoBien ? $tipoBien->rubro->codigo_rubro : '';
                            }
                            
                            $correlativo = $state ?? '';
                            $set('codigo', '266' . $codigoRubro . $correlativo);
                        }),

                    Forms\Components\TextInput::make('codigo')
                        ->label('Código de Activo Fijo')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->readOnly() // Solo lectura
                        ->dehydrated() // Asegura que se guarde en BD
                        ->extraInputAttributes(['style' => 'font-weight: bold; color: #0284c7; background-color: #f0f9ff;']),

                    Forms\Components\TextInput::make('costo')
                        ->label('Costo de Adquisición')
                        ->numeric()
                        ->inputMode('decimal')
                        ->prefix('Bs.') 
                        ->maxValue(99999999.99)
                        ->nullable(),    
                ])->columns(2),

            // SECCIÓN: DETALLES Y ESTADO
            \Filament\Forms\Components\Section::make('Detalles y Estado')
                ->description('Información descriptiva y disponibilidad actual del bien.')
                ->icon('heroicon-o-clipboard-document-check')
                ->schema([
                    Forms\Components\TextInput::make('descripcion')
                        ->label('Descripción Detallada')
                        ->required()
                        ->columnSpanFull(),

                    Forms\Components\Select::make('estado')
                        ->label('Estado Actual')
                        ->options([
                            'DISPONIBLE' => '🟢 Disponible',
                            'ENTREGADO' => '🔴 Entregado (En uso)',
                            'MANTENIMIENTO' => '🟠 En Mantenimiento',
                        ])
                        ->default('DISPONIBLE')
                        ->native(false) 
                        ->required()
                        ->columnSpan(1),
                ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('codigo')
                    ->label('Código')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->color('primary')
                    ->copyable()
                    ->copyMessage('Código copiado al portapapeles'),

                Tables\Columns\TextColumn::make('descripcion')
                    ->label('Descripción')
                    ->searchable()
                    ->wrap()
                    ->limit(50),

                Tables\Columns\TextColumn::make('tipoBien.descripcion')
                    ->label('Categoría')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('tipoBien.rubro.descripcion')
                    ->label('Rubro Presupuestario')
                    ->color('gray')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('estado')
                    ->label('Estado')
                    ->searchable() // Buscador de estado activado
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'DISPONIBLE' => 'success',
                        'ENTREGADO' => 'danger',
                        'MANTENIMIENTO' => 'warning',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('costo')
                    ->label('Costo')
                    ->money('BOB') 
                    ->sortable()
                    ->searchable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('estado')
                    ->label('Filtrar por Estado')
                    ->options([
                        'DISPONIBLE' => 'Disponibles',
                        'ENTREGADO' => 'Entregados',
                        'MANTENIMIENTO' => 'En Mantenimiento',
                    ]),
                
                Tables\Filters\SelectFilter::make('id_tipo_bien')
                    ->label('Filtrar por Categoría')
                    ->relationship('tipoBien', 'descripcion')
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->hidden(fn () => Auth::user()?->rol === 'responsable'),
            ])
            //  FUNCION DE TRANSEFERIR 
            ->bulkActions([
                Tables\Actions\BulkAction::make('transferir')
                    ->visible(fn () => Auth::user()?->rol === 'responsable') // Solo para responsables
                    ->label('Transferir Bienes')
                    ->icon('heroicon-o-arrows-right-left')
                    ->color('warning')
                    ->modalHeading('Formulario de Transferencia Interna')
                    ->modalWidth('5xl')
                    ->form([

                        \Filament\Forms\Components\Section::make('Sus Datos (Funcionario que Transfiere)')
                            ->schema([
                                \Filament\Forms\Components\Grid::make(3)
                                    ->schema([
                                        \Filament\Forms\Components\Placeholder::make('sol_nombre')
                                            ->label('Apellidos y Nombres:')
                                            ->content(fn () => Auth::user()?->responsable?->nombre_apellido ?? 'N/D')
                                            ->extraAttributes(['class' => 'font-semibold text-primary-600']),
                                        
                                        \Filament\Forms\Components\Placeholder::make('sol_ci')
                                            ->label('Nro. Documento:')
                                            ->content(fn () => Auth::user()?->responsable?->ci ?? 'N/D'),
                                            
                                        \Filament\Forms\Components\Placeholder::make('sol_cargo')
                                            ->label('Cargo:')
                                            ->content(fn () => Auth::user()?->responsable?->oficinaCargo?->cargo?->descripcion ?? 'N/D'),
                                    ]),
                            ]),

                        \Filament\Forms\Components\Section::make('Datos Del Funcionario Receptor')
                            ->schema([
                                \Filament\Forms\Components\Select::make('id_receptor')
                                    ->label('Buscar Funcionario (Apellidos y Nombres)')
                                    ->options(function () {
                                        $adminIds = \App\Models\User::where('rol', 'admin')
                                            ->whereNotNull('responsable_id')
                                            ->pluck('responsable_id')
                                            ->toArray();

                                        $miId = Auth::user()?->responsable_id;
                                        if ($miId && !in_array($miId, $adminIds)) {
                                            $adminIds[] = $miId;
                                        }

                                        $query = \App\Models\Responsable::query();
                                        
                                        if (!empty($adminIds)) {
                                            $query->whereNotIn('idresponsables', $adminIds);
                                        }

                                        $query->whereRaw('UPPER(nombre_apellido) NOT LIKE ?', ['%ADMIN%']);
                                        return $query->pluck('nombre_apellido', 'idresponsables');
                                    })
                                    ->searchable()
                                    ->live()
                                    ->required(),

                                \Filament\Forms\Components\Grid::make(3)
                                    ->schema([
                                        \Filament\Forms\Components\Placeholder::make('rec_ci')
                                            ->label('Nro. Documento:')
                                            ->content(function (\Filament\Forms\Get $get) {
                                                if (!$get('id_receptor')) return '--';
                                                return \App\Models\Responsable::find($get('id_receptor'))?->ci ?? 'N/D';
                                            }),
                                            
                                        \Filament\Forms\Components\Placeholder::make('rec_oficina')
                                            ->label('Oficina:')
                                            ->content(function (\Filament\Forms\Get $get) {
                                                if (!$get('id_receptor')) return '--';
                                                $receptor = \App\Models\Responsable::with('oficinaCargo.oficina')->find($get('id_receptor'));
                                                return $receptor?->oficinaCargo?->oficina?->descripcion ?? 'N/D';
                                            }),
                                            
                                        \Filament\Forms\Components\Placeholder::make('rec_cargo')
                                            ->label('Cargo:')
                                            ->content(function (\Filament\Forms\Get $get) {
                                                if (!$get('id_receptor')) return '--';
                                                $receptor = \App\Models\Responsable::with('oficinaCargo.cargo')->find($get('id_receptor'));
                                                return $receptor?->oficinaCargo?->cargo?->descripcion ?? 'N/D';
                                            }),
                                    ]),
                            ]),
                            
                        \Filament\Forms\Components\Textarea::make('observaciones')
                            ->label('Observaciones de la Transferencia')
                            ->rows(2),
                    ])
                    ->action(function (\Illuminate\Database\Eloquent\Collection $records, array $data) {
                        $numeroActa = 'TR-' . date('Ymd-His');

                        $nuevaActa = \App\Models\Acta::create([
                            'numero_acta' => $numeroActa,
                            'tipo' => 'TRANSFERENCIA INTERNA',
                            'id_responsables' => $data['id_receptor'],
                            'observaciones' => $data['observaciones'] ?? null,
                        ]);

                        foreach ($records as $bien) {
                            \App\Models\ActaItem::create([
                                'id_acta' => $nuevaActa->getKey(), 
                                'id_bienes' => $bien->getKey(),
                                'estado' => 'Bueno', 
                            ]);
                        }

                        \Filament\Notifications\Notification::make()
                            ->success()
                            ->title('Transferencia Realizada')
                            ->body("Se transfirieron {$records->count()} bienes y se generó el acta {$numeroActa}.")
                            ->actions([
                                \Filament\Notifications\Actions\Action::make('imprimir')
                                    ->label('Descargar PDF')
                                    ->button()
                                    ->color('danger')
                                    ->url(route('acta.imprimir', $nuevaActa), shouldOpenInNewTab: true),
                            ])
                            ->send();
                    })
                    ->deselectRecordsAfterCompletion(),
            ])
            ->defaultSort('codigo', 'asc');
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = Auth::user();

        if ($user && $user->rol === 'responsable') {
            $query->whereIn('idbienes', function ($subquery) use ($user) {
                $subquery->select('ai.id_bienes')
                         ->from('acta_items as ai')
                         ->join('actas as a', 'a.idacta', '=', 'ai.id_acta')
                         ->where('a.id_responsables', $user->responsable_id)
                         ->where('a.tipo', '!=', 'DEVOLUCION')
                         ->whereRaw('a.idacta = (SELECT MAX(a2.idacta) FROM acta_items as ai2 INNER JOIN actas as a2 ON a2.idacta = ai2.id_acta WHERE ai2.id_bienes = ai.id_bienes)');
            });
        }

        return $query;
    }

    public static function canCreate(): bool
    {
        return Auth::user()?->rol === 'admin';
    }

    public static function canEdit(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return Auth::user()?->rol === 'admin';
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBiens::route('/'),
            'create' => Pages\CreateBien::route('/create'),
            'edit' => Pages\EditBien::route('/{record}/edit'),
        ];
    }
}