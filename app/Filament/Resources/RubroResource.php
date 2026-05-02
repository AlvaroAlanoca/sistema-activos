<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RubroResource\Pages;
use App\Filament\Resources\RubroResource\RelationManagers;
use App\Models\Rubro;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class RubroResource extends Resource
{
    protected static ?string $model = Rubro::class;
    protected static ?string $navigationGroup = 'Gestión de Inventario';
    protected static ?int $navigationSort = 3;

    protected static ?string $navigationIcon = 'heroicon-o-archive-box';

public static function form(Form $form): Form
    {
        return $form->schema([
            
            \Filament\Forms\Components\Section::make('Información del Rubro')
                ->description('Defina la codificación y clasificación presupuestaria.')
                ->icon('heroicon-o-rectangle-group')
                ->schema([
                    
                    // El nuevo campo que me pediste agregar
                    \Filament\Forms\Components\TextInput::make('codigo_rubro')
                        ->label('Código de Rubro')
                        ->placeholder('Ej: RUB-01')
                        ->required()
                        ->unique(ignoreRecord: true), // Evita que registres el mismo código dos veces

                    // Tu campo original de clasificador
                    \Filament\Forms\Components\TextInput::make('clasificador_presupuestario')
                        ->label('Clasificador Presupuestario')
                        ->placeholder('Ej: 49100')
                        ->required(),

                    // Tu campo original de descripción
                    \Filament\Forms\Components\TextInput::make('descripcion')
                        ->label('Descripción del Rubro')
                        ->required()
                        ->columnSpanFull(), // Hace que ocupe todo el ancho inferior

                ])->columns(2), // Coloca el código y el clasificador uno al lado del otro
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
            Tables\Columns\TextColumn::make('descripcion')->label('Descripción')->searchable(),
            Tables\Columns\TextColumn::make('clasificador_presupuestario')->label('Clasificador')->searchable(),
            
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
            'index' => Pages\ListRubros::route('/'),
            'create' => Pages\CreateRubro::route('/create'),
            'edit' => Pages\EditRubro::route('/{record}/edit'),
        ];
    }
}
