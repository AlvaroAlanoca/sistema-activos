<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BienResource\Pages;
use App\Filament\Resources\BienResource\RelationManagers;
use App\Models\Bien;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;


class BienResource extends Resource
{
    protected static ?string $model = Bien::class;
    protected static ?string $navigationGroup = 'Gestión de Inventario';
    protected static ?int $navigationSort = 1;
    protected static ?string $navigationLabel = 'Bienes';

    protected static ?string $navigationIcon = 'heroicon-o-archive-box';

public static function form(Form $form): Form
    {
        return $form->schema([
            
            \Filament\Forms\Components\Section::make('Clasificación y Codificación')
                ->description('Asigne el tipo de bien, correlativo y código patrimonial del activo.')
                ->icon('heroicon-o-qr-code')
                ->schema([
                    Forms\Components\Select::make('id_tipo_bien')
                        ->label('Tipo de Bien')
                        ->relationship('tipoBien', 'descripcion')
                        ->searchable() // Agrega buscador al menú
                        ->preload()    // Carga las opciones más rápido
                        ->required(),

                    Forms\Components\TextInput::make('correlativo')
                        ->label('Correlativo')
                        ->numeric()
                        ->required(),

                    Forms\Components\TextInput::make('codigo')
                        ->label('Código de Activo Fijo')
                        ->required(),
                ])->columns(3), // Ponemos estos 3 campos en una sola fila

            \Filament\Forms\Components\Section::make('Detalles y Estado')
                ->description('Información descriptiva y disponibilidad actual del bien.')
                ->icon('heroicon-o-clipboard-document-check')
                ->schema([
                    Forms\Components\TextInput::make('descripcion')
                        ->label('Descripción Detallada')
                        ->required()
                        ->columnSpanFull(), // Hace que ocupe todo el ancho para escribir cómodamente

                    Forms\Components\Select::make('estado')
                        ->label('Estado Actual')
                        ->options([
                            'DISPONIBLE' => '🟢 Disponible',
                            'ENTREGADO' => '🔴 Entregado (En uso)',
                            'MANTENIMIENTO' => '🟠 En Mantenimiento',
                        ])
                        ->default('DISPONIBLE')
                        ->native(false) // Le da el diseño moderno de Filament en lugar del diseño gris del navegador
                        ->required()
                        ->columnSpan(1), // Evita que el botón de estado se estire por toda la pantalla
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
                ->copyable(), // Permite copiar el código con un clic

            Tables\Columns\TextColumn::make('descripcion')
                ->label('Descripción')
                ->searchable(),

            // Mostramos el Tipo de Bien usando la relación
            Tables\Columns\TextColumn::make('tipoBien.descripcion')
                ->label('Categoría')
                ->sortable(),

            // Mostramos el Rubro (saltando a través de TipoBien)
            Tables\Columns\TextColumn::make('tipoBien.rubro.descripcion')
                ->label('Rubro Presupuestario')
                ->color('gray'),

            Tables\Columns\TextColumn::make('estado')
            ->badge()
            ->color(fn (string $state): string => match ($state) {
            'DISPONIBLE' => 'success',
            'ENTREGADO' => 'danger',
            'MANTENIMIENTO' => 'warning',
            default => 'gray',
            }),
            ])
            ->filters([
                //
            ])
            ->actions([
            Tables\Actions\EditAction::make()
                    // Ocultamos el botón de editar para los responsables
                    ->hidden(fn () => Auth::user()?->rol === 'responsable'),
            ])
            ->bulkActions([
                Tables\Actions\BulkAction::make('transferir')
                        ->label('Transferir Bienes')
                        ->icon('heroicon-o-arrows-right-left')
                        ->color('warning')
                        ->modalHeading('Formulario de Transferencia Interna')
                        ->modalDescription('Verifique sus datos y seleccione al funcionario que recibirá los bienes marcados.')
                        ->modalWidth('5xl') // Hacemos la ventana bien ancha para que quepa todo el formulario
                        ->form([
                            
                            // SECCIÓN 1: DATOS DEL SOLICITANTE (El usuario actual)
                            \Filament\Forms\Components\Section::make('Sus Datos (Funcionario que Transfiere)')
                                ->schema([
                                    \Filament\Forms\Components\Grid::make(3)
                                        ->schema([
                                            \Filament\Forms\Components\Placeholder::make('sol_nombre')
                                                ->label('Apellidos y Nombres:')
                                                // Sacamos los datos directo de la sesión iniciada
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

                            // SECCIÓN 2: DATOS DEL RECEPTOR (Dinámico, igual que en tu acta)
                            \Filament\Forms\Components\Section::make('Datos Del Funcionario Receptor')
                                ->schema([
                                    \Filament\Forms\Components\Select::make('id_receptor')
                                        ->label('Buscar Funcionario (Apellidos y Nombres)')
                                        ->options(function () {
                                            // Filtramos para que no pueda transferirse a sí mismo
                                            $miId = Auth::user()?->responsable_id;
                                            return \App\Models\Responsable::where('idresponsables', '!=', $miId)
                                                ->pluck('nombre_apellido', 'idresponsables');
                                        })
                                        ->searchable()
                                        ->live() // Recarga la pantalla al elegir
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
                                
                            // SECCIÓN 3: OBSERVACIONES
                            \Filament\Forms\Components\Textarea::make('observaciones')
                                ->label('Observaciones de la Transferencia')
                                ->rows(2),
                        ])
                        ->action(function (\Illuminate\Database\Eloquent\Collection $records, array $data) {
                            
                            // 1. Generamos un número de acta único (Ej: TR-20260501-153022)
                            $numeroActa = 'TR-' . date('Ymd-His');

                            // 2. Creamos la nueva Acta
                            $nuevaActa = \App\Models\Acta::create([
                                'numero_acta' => $numeroActa,
                                'tipo' => 'TRANSFERENCIA INTERNA',
                                'id_responsables' => $data['id_receptor'],
                                'observaciones' => $data['observaciones'] ?? null,
                            ]);

                            // 3. Vinculamos los bienes seleccionados a la nueva acta
                            foreach ($records as $bien) {
                                \App\Models\ActaItem::create([
                                    'id_acta' => $nuevaActa->getKey(), 
                                    'id_bienes' => $bien->getKey(),
                                    'estado' => 'Bueno', 
                                ]);
                            }

                            // 4. Mostramos notificación de éxito con el botón del PDF
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
                
            ]);
    }
public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $query = parent::getEloquentQuery();
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        // Si es un responsable, filtramos para que vea solo sus bienes actuales
        if ($user && $user->rol === 'responsable') {
            $query->whereIn('idbienes', function ($subquery) use ($user) {
                
                $subquery->select('ai.id_bienes')
                         ->from('acta_items as ai')
                         ->join('actas as a', 'a.idacta', '=', 'ai.id_acta')
                         ->where('a.id_responsables', $user->responsable_id)
                         ->where('a.tipo', '!=', 'DEVOLUCION')
                         
                         // LA MAGIA: Asegurarnos de que evaluamos solo la ÚLTIMA acta generada para este bien
                         ->whereRaw('a.idacta = (SELECT MAX(a2.idacta) FROM acta_items as ai2 INNER JOIN actas as a2 ON a2.idacta = ai2.id_acta WHERE ai2.id_bienes = ai.id_bienes)');
            });
        }

        return $query;
    }



    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getNavigationLabel(): string
    {
        return Auth::user()?->rol === 'responsable' ? 'Mis Bienes Asignados' : 'Gestión de Bienes';
    }
    // 1. Bloquea el botón superior derecho y la pantalla de "Crear"
    public static function canCreate(): bool
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        
        return $user && $user->rol === 'admin';
    }

    // 2. Bloquea la pantalla de "Editar" y quita el click a la fila
    public static function canEdit(\Illuminate\Database\Eloquent\Model $record): bool
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        
        return $user && $user->rol === 'admin';
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
