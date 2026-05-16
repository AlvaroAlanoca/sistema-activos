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
                    ->description('Registre los datos de la empresa proveedora y su código de contratación.')
                    ->schema([
                        Forms\Components\TextInput::make('cuce')
                            ->label('Código CUCE')
                            ->required()
                            ->unique(ignoreRecord: true) // Evita registrar dos veces el mismo código estatal
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
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('cuce')
                    ->label('CUCE')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info'), // Le da un color azulito institucional al código
                    
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