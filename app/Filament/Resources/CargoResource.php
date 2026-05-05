<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CargoResource\Pages;
use App\Models\Cargo;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class CargoResource extends Resource
{
    protected static ?string $model = Cargo::class;

    // Lo mantenemos en el mismo grupo de personal
    protected static ?string $navigationGroup = 'Administración de Personal';
    protected static ?int $navigationSort = 2; // Arriba de las oficinas y asignaciones

    // Textos limpios para la interfaz
    protected static ?string $modelLabel = 'Cargo';
    protected static ?string $pluralModelLabel = 'Cargos';
    protected static ?string $navigationLabel = 'Cargos';

    // Usamos un icono de "identificación/gafete" que va perfecto con los puestos de trabajo
    protected static ?string $navigationIcon = 'heroicon-o-identification';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Detalles del Cargo')
                ->description('Registre el nombre del puesto de trabajo según el manual de funciones.')
                ->icon('heroicon-o-briefcase')
                ->schema([
                    Forms\Components\TextInput::make('descripcion')
                        ->label('Denominación del Cargo')
                        ->placeholder('Ej: Director General, Auditor Interno, Encargado de Activos...')
                        ->required()
                        ->maxLength(255)
                        ->columnSpanFull(), // Ocupa todo el ancho
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('descripcion')
                    ->label('Cargo / Puesto de Trabajo')
                    ->searchable()
                    ->sortable()
                    ->weight('bold') 
                    ->color('black'), 
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(), // Botón individual de borrar
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('descripcion', 'asc'); // Orden alfabético por defecto
    }

    public static function canViewAny(): bool
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        
        // Solo los administradores gestionan los cargos
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
            'index' => Pages\ListCargos::route('/'),
            'create' => Pages\CreateCargo::route('/create'),
            'edit' => Pages\EditCargo::route('/{record}/edit'),
        ];
    }
}