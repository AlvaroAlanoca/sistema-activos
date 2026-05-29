<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ServicioGasolinaResource\Pages;
use App\Models\ServicioGasolina;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ServicioGasolinaResource extends Resource
{
    protected static ?string $model = ServicioGasolina::class;

    // Ajustes de Ubicación en el Menú Lateral (Agrupado junto a Catálogo de Servicios)
    protected static ?string $navigationIcon = 'heroicon-o-ticket'; 
    protected static ?string $navigationLabel = 'Control de Gasolina / Vales';
    protected static ?string $navigationGroup = 'Contratos'; 
    protected static ?int $navigationSort = 1; 

    protected static ?string $modelLabel = 'Vale de Combustible';
    protected static ?string $pluralModelLabel = 'Vales de Combustible';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Registro de Vale de Combustible')
                    ->description('Complete los datos de la carga de combustible del vehículo.')
                    ->icon('heroicon-o-fire')
                    ->schema([
                        
                        // 1. Vincular al Contrato de Gasolina (Solo muestra servicios de Combustible)
                        Forms\Components\Select::make('id_servicio')
                            ->label('Contrato / Proveedor de Combustible')
                            ->relationship(
                                name: 'servicio',
                                titleAttribute: 'empresa',
                                modifyQueryUsing: fn (Builder $query) => $query->where('tipo_servicio', 'COMBUSTIBLE')
                            )
                            ->getOptionLabelFromRecordUsing(fn ($record) => "[CUCE: {$record->cuce}] - {$record->empresa}")
                            ->searchable()
                            ->preload()
                            ->required()
                            ->native(false),

                        // 2. Fecha del Vale de Carga
                        Forms\Components\DatePicker::make('fecha_vale')
                            ->label('Fecha del Vale')
                            ->default(now())
                            ->displayFormat('d/m/Y')
                            ->native(false)
                            ->required(),

                        // 3. Control de Placa del Vehículo
                        Forms\Components\TextInput::make('placa')
                            ->label('Placa del Vehículo')
                            ->required()
                            ->maxLength(20)
                            ->placeholder('Ej: 4829XYZ')
                            // Micro-interacción: Convierte el texto a mayúsculas automáticamente en tiempo real
                            ->extraInputAttributes([
                                'style' => 'text-transform: uppercase; font-weight: bold; letter-spacing: 1px;',
                            ])
                            ->dehydrateStateUsing(fn ($state) => strtoupper($state)),

                        // 4. Cantidad de Litros Despachados
                        Forms\Components\TextInput::make('cantidad_litros')
                            ->label('Cantidad Cargada')
                            ->numeric()
                            ->prefix('Lts.')
                            ->minValue(0.01)
                            ->required()
                            ->placeholder('0.00'),

                        // 5. Usuario que registra (Oculto y automático para resguardar la autoría)
                        Forms\Components\Hidden::make('id_user')
                            ->default(fn () => Auth::id())
                            ->required(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('fecha_vale')
                    ->label('Fecha del Vale')
                    ->date('d/m/Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('placa')
                    ->label('Placa Vehículo')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('gray')
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('cantidad_litros')
                    ->label('Volumen Despachado')
                    ->numeric(2)
                    ->suffix(' Lts.')
                    ->weight('bold')
                    ->color('primary')
                    ->sortable()
    // 👇 ESTA LÍNEA HACE LA SUMATORIA AUTOMÁTICA DE LO QUE ESTÁ EN PANTALLA 👇
                ->summarize(\Filament\Tables\Columns\Summarizers\Sum::make()->label('Total Litros')),

                Tables\Columns\TextColumn::make('servicio.empresa')
                    ->label('Proveedor / Estación')
                    ->searchable()
                    ->sortable(),
                    Tables\Columns\TextColumn::make('servicio.cantidad_litros')
    ->label('Total Contrato')
    ->numeric(2)
    ->suffix(' Lts.')
    ->color('gray')
    ->toggleable(isToggledHiddenByDefault: true), // Oculto por defecto para no saturar

Tables\Columns\TextColumn::make('saldo_contrato')
    ->label('Saldo del Contrato')
    // 👇 CÁLCULO VISUAL AL VUELO 👇
    ->getStateUsing(function ($record) {
        if (!$record->servicio || !$record->servicio->cantidad_litros) {
            return 0;
        }
        // Sumamos absolutamente todos los vales despachados para este contrato específico
        $totalConsumido = \App\Models\ServicioGasolina::where('id_servicio', $record->id_servicio)->sum('cantidad_litros');
        
        // Retornamos la resta (Litros Originales - Litros Consumidos)
        return $record->servicio->cantidad_litros - $totalConsumido;
    })
    ->numeric(2)
    ->suffix(' Lts.')
    ->badge()
    ->color(fn ($state) => $state <= 0 ? 'danger' : ($state < 500 ? 'warning' : 'success')) // Semáforo de alerta
    ->sortable(false),

                Tables\Columns\TextColumn::make('servicio.cuce')
                    ->label('CUCE Contrato')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('user.responsable.nombre_apellido')
                    ->label('Funcionario (Registrado Por)')
                    ->color('gray')
                    ->sortable()
                    ->searchable()
                    ->default('Administrador del Sistema'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha Registro Sistema')
                    ->dateTime('d/m/Y H:i')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\Filter::make('fecha_vale')
                    ->form([
                        Forms\Components\DatePicker::make('desde')->label('Desde')->native(false),
                        Forms\Components\DatePicker::make('hasta')->label('Hasta')->native(false),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['desde'], fn ($q, $date) => $q->whereDate('fecha_vale', '>=', $date))
                            ->when($data['hasta'], fn ($q, $date) => $q->whereDate('fecha_vale', '<=', $date));
                    }),
            ])
->actions([
            Tables\Actions\EditAction::make(),
        ])
        // 👇 AÑADIMOS EL BOTÓN EN LA PARTE SUPERIOR DE LA TABLA 👇
->headerActions([
    Tables\Actions\Action::make('reporte_placas')
        ->label('Resumen por Placas (PDF)')
        ->icon('heroicon-o-document-chart-bar')
        ->color('danger')
        ->form([
            \Filament\Forms\Components\Grid::make(2)
                ->schema([
                    \Filament\Forms\Components\DatePicker::make('desde')
                        ->label('Fecha Inicio')
                        ->required()
                        ->native(false)
                        ->default(now()->startOfMonth()),
                    \Filament\Forms\Components\DatePicker::make('hasta')
                        ->label('Fecha Fin')
                        ->required()
                        ->native(false)
                        ->default(now()),
                ]),

            // 👇 NUEVO: Desplegable dinámico y múltiple para las placas 👇
            \Filament\Forms\Components\Select::make('placas')
                ->label('Filtrar por Placa(s)')
                ->placeholder('Todas las placas (Dejar vacío para ver todo)')
                ->options(function () {
                    // Extrae las placas únicas directamente de la base de datos para el desplegable
                    return \App\Models\ServicioGasolina::distinct()
                        ->whereNotNull('placa')
                        ->pluck('placa', 'placa')
                        ->toArray();
                })
                ->multiple() // Permite escoger 1, varias o ninguna
                ->searchable() // Habilita buscador interno en el desplegable
                ->preload() // Precarga los datos para mayor velocidad
                ->native(false),
        ])
->action(function (array $data, \Livewire\Component $livewire) {
    // 1. Construimos la URL completa con todos los parámetros ingresados en el formulario
    $url = route('gasolina.reporte', [
        'desde' => $data['desde'],
        'hasta' => $data['hasta'],
        'placas' => $data['placas'] ?? [],
    ]);

    // 2. Usamos el motor de Livewire para ejecutar JavaScript puro en el navegador del cliente
    // Esto fuerza la apertura del PDF en una pestaña nueva sin perder tu trabajo actual.
    $livewire->js("window.open('{$url}', '_blank');");
}),
])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                //    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('fecha_vale', 'desc');
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
            'index' => Pages\ListServicioGasolinas::route('/'),
            'create' => Pages\CreateServicioGasolina::route('/create'),
            'edit' => Pages\EditServicioGasolina::route('/{record}/edit'),
        ];
    }
}