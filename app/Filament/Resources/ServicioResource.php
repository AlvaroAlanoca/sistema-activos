<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ServicioResource\Pages;
use App\Models\Servicio;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ServicioResource extends Resource
{
    protected static ?string $model = Servicio::class;

    // Ícono representativo para los servicios/empresas
    protected static ?string $navigationIcon = 'heroicon-o-briefcase'; 
    protected static ?string $navigationLabel = 'Catálogo de Servicios';
    
    // ¡AQUÍ ESTÁ LA MAGIA! Lo metemos al mismo grupo que el recurso anterior
    protected static ?string $navigationGroup = 'Contratos'; 
    
    // Le ponemos 0 para que aparezca arriba de "Contratos" (que le pusimos 1)
    protected static ?int $navigationSort = 0; 

public static function form(Form $form): Form
{
    return $form
        ->schema([
            Forms\Components\Section::make('Datos del Servicio')
                ->description('Registre los datos de la empresa proveedora y el origen del servicio.')
                ->schema([
                    // NUEVO CAMPO: Selector de Origen / Tipo de Servicio
                    Forms\Components\Select::make('tipo')
                        ->label('Tipo de Servicio / Origen')
                        ->options([
                            'DDELPZ' => 'De la empresa DDELPZ',
                            'SICOES' => 'De SICOES',
                        ])
                        ->required()
                        ->native(false) // Le da una estética visual más moderna desplegable
                        ->default('SICOES'), // Opción por defecto
                        
                    Forms\Components\TextInput::make('cuce')
                        ->label('Código')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->maxLength(255),
                        
                    Forms\Components\TextInput::make('empresa')
                        ->label('Nombre de la Empresa / Proveedor')
                        ->required()
                        ->maxLength(255),
                        
                    Forms\Components\Textarea::make('descripcion')
                        ->label('Descripción General del Servicio')
                        ->placeholder('Detalles adicionales sobre lo que provee esta empresa...')
                        ->rows(3)
                        ->columnSpanFull(),
                ])->columns(3), // Cambiamos a 3 columnas para que encajen perfecto los primeros campos
        ]);
}

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('tipo')
                ->label('Origen')
                ->badge()
                ->color(fn (string $state): string => match ($state) {
                    'DDELPZ' => 'success', // Color verde institucional
                    'SICOES' => 'warning', // Color naranja de advertencia/proceso
                    default => 'gray',
                })
                ->sortable(),
                Tables\Columns\TextColumn::make('cuce')
                    ->label('CUCE')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info'), // Le da un color azulito institucional al código
                 Tables\Columns\TextColumn::make('descripcion')
                    ->label('Descripcion del Servicio')
                    ->searchable()
                    ->sortable()
                    ->limit(50),
                   
                Tables\Columns\TextColumn::make('empresa')
                    ->label('Empresa Proveedora')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha de Registro')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true), // Oculto por defecto para no saturar
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