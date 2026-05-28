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
                    ->sortable(),

                Tables\Columns\TextColumn::make('servicio.empresa')
                    ->label('Proveedor / Estación')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('servicio.cuce')
                    ->label('CUCE Contrato')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Registrado Por')
                    ->color('gray')
                    ->sortable(),

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