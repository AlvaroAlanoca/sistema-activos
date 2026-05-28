<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ServicioResource\Pages;
use App\Models\Servicio;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ServicioResource extends Resource
{
    protected static ?string $model = Servicio::class;

    protected static ?string $navigationIcon = 'heroicon-o-briefcase'; 
    protected static ?string $navigationLabel = 'Catálogo de Servicios';
    protected static ?string $navigationGroup = 'Contratos'; 
    protected static ?int $navigationSort = 0; 

public static function form(Form $form): Form
{
    return $form
        ->schema([
            // SECCIÓN 1: DATOS PRINCIPALES (REACTIVA)
            Forms\Components\Section::make('Datos del Servicio')
                ->description('Registre los datos de la empresa proveedora y el origen del servicio.')
                ->schema([
                    
                    // 1. Selector de Origen (Campo Padre Principal)
                    Forms\Components\Select::make('tipo')
                        ->label('Tipo de Servicio / Origen')
                        ->options([
                            'DDELPZ' => 'De la empresa DDELPZ',
                            'SICOES' => 'De SICOES',
                        ])
                        ->required()
                        ->native(false) 
                        ->default('SICOES')
                        ->live() // Activa la reactividad en tiempo real
                        ->afterStateUpdated(function (Forms\Set $set, $state) {
                            // Si cambia a algo diferente de DDELPZ, limpiamos los campos dependientes
                            if ($state !== 'DDELPZ') {
                                $set('tipo_servicio', null);
                                $set('cantidad_litros', null);
                            }
                        }),

                    // 2. Tipo de Servicio (Aparece RECIÉN si el origen es "DDELPZ")
                    Forms\Components\Select::make('tipo_servicio')
                        ->label('Tipo de Servicio')
                        ->options([
                            'COMBUSTIBLE' => '⛽ Suministro de Combustible (Gasolina)',
                            'MANTENIMIENTO' => '🛠️ Mantenimiento Vehicular / Edificios',
                            'SEGUROS' => '🛡️ Seguros y Pólizas',
                            'CONSULTORIA' => '📋 Servicios de Consultoría',
                            'OTROS' => '💼 Otros Servicios',
                        ])
                        ->required(fn (Forms\Get $get) => $get('tipo') === 'DDELPZ') // Solo es obligatorio si es visible
                        ->native(false)
                        ->live() // Activa la reactividad para el campo de litros
                        ->visible(fn (Forms\Get $get) => $get('tipo') === 'DDELPZ') // Condición de visibilidad estricta
                        ->afterStateUpdated(function (Forms\Set $set, $state) {
                            // Si cambian a un servicio que no es combustible, limpiamos los litros inmediatamente
                            if ($state !== 'COMBUSTIBLE') {
                                $set('cantidad_litros', null);
                            }
                        }),

                    Forms\Components\TextInput::make('cuce')
                        ->label('Código / CUCE')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->maxLength(255),
                        
                    Forms\Components\TextInput::make('empresa')
                        ->label('Nombre de la Empresa / Proveedor')
                        ->required()
                        ->maxLength(255),

                    // 3. Cantidad de Litros (Habilitado RECIÉN si el servicio es "COMBUSTIBLE")
                    Forms\Components\TextInput::make('cantidad_litros')
                        ->label('Cantidad (Litros)')
                        ->numeric()
                        ->minValue(0)
                        ->placeholder('Ej: 500')
                        ->nullable()
                        // Se muestra únicamente si el origen es DDELPZ para mantener limpia la cuadrícula
                        ->visible(fn (Forms\Get $get) => $get('tipo') === 'DDELPZ')
                        // Se desactiva/bloquea si el tipo de servicio seleccionado no es COMBUSTIBLE
                        ->disabled(fn (Forms\Get $get) => $get('tipo_servicio') !== 'COMBUSTIBLE')
                        // Fuerza a Laravel a enviar un valor nulo si el campo quedó desactivado
                        ->dehydrated(fn (Forms\Get $get) => $get('tipo_servicio') === 'COMBUSTIBLE'),

                    Forms\Components\TextInput::make('monto')
                        ->label('Monto Total')
                        ->numeric()
                        ->prefix('Bs.')
                        ->minValue(0)
                        ->required()
                        ->placeholder('0.00'),

                    Forms\Components\DatePicker::make('fecha_inicio')
                        ->label('Fecha de Inicio')
                        ->native(false)
                        ->displayFormat('d/m/Y')
                        ->required(),

                    Forms\Components\DatePicker::make('fecha_final')
                        ->label('Fecha de Finalización')
                        ->native(false)
                        ->displayFormat('d/m/Y')
                        ->required()
                        ->after('fecha_inicio'), 

                    Forms\Components\TextInput::make('porcentaje_avance')
                        ->label('Porcentaje de Avance')
                        ->numeric()
                        ->default(0)
                        ->minValue(0)
                        ->maxValue(100)
                        ->prefix('%')
                        ->required(),

                    Forms\Components\Textarea::make('descripcion')
                        ->label('Descripción General del Servicio')
                        ->placeholder('Detalles adicionales sobre lo que provee esta empresa...')
                        ->rows(3)
                        ->columnSpanFull(),
                ])->columns(3),

            // SECCIÓN 2: DOCUMENTOS DIGITALES (Se mantiene intacta)
            Forms\Components\Section::make('Documentación Digital de Respaldo')
                ->description('Cargue los archivos correspondientes en formato PDF o Imagen. El sistema extraerá el flujo binario para guardarlo en la base de datos.')
                ->schema([
                    self::configurarCampoBlob('convocatoria', 'Convocatoria Oficial'),
                    self::configurarCampoBlob('documento_base', 'Documento Base de Contratación (DBC)'),
                    self::configurarCampoBlob('acta_apertura', 'Acta de Apertura'),
                    self::configurarCampoBlob('resolucion_adjudicacion', 'Resolución de Adjudicación'),
                    self::configurarCampoBlob('informe', 'Informe Técnico / Final'),
                ])->columns(2),
        ]);
}

    public static function table(Table $table): Table
    {
        return $table
            ->deferLoading() // Evita congelamiento de memoria RAM al renderizar los datos BLOB
            ->columns([
                Tables\Columns\TextColumn::make('tipo')
                    ->label('Origen')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'DDELPZ' => 'success', 
                        'SICOES' => 'warning', 
                        default => 'gray',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('tipo_servicio')
                    ->label('Tipo')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('gray'),
                Tables\Columns\TextColumn::make('cuce')
                    ->label('CUCE / Código')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info'), 
                    
                Tables\Columns\TextColumn::make('empresa')
                    ->label('Empresa Proveedora')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('cantidad_litros')
                    ->label('Litros')
                    ->numeric()
                    ->sortable()
                    ->placeholder('--'),

                Tables\Columns\TextColumn::make('monto')
                    ->label('Monto')
                    ->money('BOB')
                    ->sortable(),

                Tables\Columns\TextColumn::make('porcentaje_avance')
                    ->label('% Avance')
                    ->view('filament.tables.columns.progress-bar')
                    ->searchable(), 

                // =========================================================================
                // COLUMNAS INTERACTIVAS PARA DESCARGAR LOS BINARIOS (LONGBLOB)
                // =========================================================================
                self::crearColumnaDescargaBlob('convocatoria', 'Convocatoria'),
                self::crearColumnaDescargaBlob('documento_base', 'Doc. Base'),
                self::crearColumnaDescargaBlob('acta_apertura', 'Acta Apertura'),
                self::crearColumnaDescargaBlob('resolucion_adjudicacion', 'Res. Adjudicación'),
                self::crearColumnaDescargaBlob('informe', 'Informe'),

                Tables\Columns\TextColumn::make('fecha_inicio')
                    ->label('Fecha Inicio')
                    ->toggleable(isToggledHiddenByDefault: true),      
                Tables\Columns\TextColumn::make('fecha_final')
                    ->label('Fecha Final')
                    ->toggleable(isToggledHiddenByDefault: true),     
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha de Registro')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    /**
     * Extrae el binario en bruto del archivo cargado antes de enviarlo a MySQL
     */
protected static function configurarCampoBlob(string $campo, string $etiqueta): Forms\Components\FileUpload
    {
        return Forms\Components\FileUpload::make($campo)
            ->label($etiqueta)
            ->acceptedFileTypes(['application/pdf', 'image/*'])
            ->maxSize(20480)
            ->live()
            // 👇 1. EVITA EL COLAPSO: Le dice a Filament que no intente renderizar el binario en pantalla
            ->formatStateUsing(fn () => null) 
            // 👇 2. PROTECCIÓN DE DATOS: Solo envía la orden de guardar si el usuario subió un archivo NUEVO. 
            // Si lo deja en blanco, ignora el campo y el archivo original se mantiene intacto.
            ->dehydrated(fn ($state) => filled($state)) 
            ->saveUploadedFileUsing(function ($file) {
                return file_get_contents($file->getRealPath());
            })
            // 👇 3. EXPERIENCIA DE USUARIO: Como el cuadro se verá vacío, le avisamos que el archivo ya existe.
            ->helperText(function (?Servicio $record) use ($campo) {
                // Si estamos editando y el campo tiene datos, mostramos el aviso verde
                if ($record && $record->$campo) {
                    return new \Illuminate\Support\HtmlString('<span style="color: #10b981; font-weight: bold; display: flex; align-items: center; gap: 4px;"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg> Documento ya registrado. Suba uno nuevo solo si desea reemplazarlo.</span>');
                }
                // Si es un registro nuevo o no hay archivo, mostramos un texto normal
                return 'No hay archivo registrado.';
            })
            ->nullable();
    }

    /**
     * Construye una columna con descarga por Streaming directo desde los datos de la BD
     */
    protected static function crearColumnaDescargaBlob(string $campo, string $etiqueta): Tables\Columns\TextColumn
    {
        return Tables\Columns\TextColumn::make($campo)
            ->label($etiqueta)
            ->formatStateUsing(fn ($state) => $state ? '📄 Descargar' : '❌ N/D')
            ->color(fn ($state) => $state ? 'info' : 'gray')
            ->weight(fn ($state) => $state ? 'bold' : 'normal')
            ->action(function ($record) use ($campo, $etiqueta) {
                if (!$record->$campo) {
                    return;
                }

                $nombreArchivo = str_replace(' ', '_', $etiqueta);
                
                return response()->streamDownload(
                    function () use ($record, $campo) {
                        echo $record->$campo;
                    },
                    "{$nombreArchivo}_CUCE_{$record->cuce}.pdf",
                    ['Content-Type' => 'application/pdf']
                );
            });
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
            'index' => Pages\ListServicios::route('/'),
            'create' => Pages\CreateServicio::route('/create'),
            'edit' => Pages\EditServicio::route('/{record}/edit'),
        ];
    }
}