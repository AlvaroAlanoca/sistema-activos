<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OficinaCargoResource\Pages;
use App\Models\OficinaCargo;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class OficinaCargoResource extends Resource
{
    protected static ?string $model = OficinaCargo::class;


    protected static ?string $navigationGroup = 'Administración de Personal';
    protected static ?int $navigationSort = 3;
    
   
    protected static ?string $modelLabel = 'Asignación Oficina-Cargo';
    protected static ?string $pluralModelLabel = 'Oficinas y Cargos';
    protected static ?string $navigationLabel = 'Asignar Cargos';

    
    protected static ?string $navigationIcon = 'heroicon-o-link';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Relación Estructural')
                ->description('Vincule un cargo específico a un departamento u oficina de la institución.')
                ->icon('heroicon-o-briefcase')
                ->schema([
                    Forms\Components\Select::make('id_oficinas')
                        ->label('Seleccione la Oficina')
                        ->relationship('oficina', 'descripcion')
                        ->searchable() 
                        ->preload()
                        ->required()
                        ->columnSpan(1),
                        
                    Forms\Components\Select::make('id_cargos')
                        ->label('Seleccione el Cargo')
                        ->relationship('cargo', 'descripcion')
                        ->searchable() 
                        ->preload()
                        ->required()
                        ->columnSpan(1),
                ])->columns(2), // Pone ambos desplegables uno al lado del otro
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('oficina.descripcion')
                    ->label('Departamento / Oficina')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'), // La oficina va en negrita por ser el contenedor padre

                Tables\Columns\TextColumn::make('cargo.descripcion')
                    ->label('Cargo Asignado')
                    ->searchable()
                    ->sortable()
                    ->badge() // Etiqueta para diferenciarlo visualmente
                    ->color('info'), // Color azul claro
            ])
            ->filters([

            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(), // Agregado botón individual
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('oficina.descripcion', 'asc'); // Agrupa alfabéticamente por oficina al inicio
    }

    public static function canViewAny(): bool
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        
        // Solo retorna "true" (mostrar) si el usuario es administrador
        return $user && $user->rol === 'admin';
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
            'index' => Pages\ListOficinaCargos::route('/'),
            'create' => Pages\CreateOficinaCargo::route('/create'),
            'edit' => Pages\EditOficinaCargo::route('/{record}/edit'),
        ];
    }
}