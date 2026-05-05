<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OficinaResource\Pages;
use App\Models\Oficina;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class OficinaResource extends Resource
{
    protected static ?string $model = Oficina::class;
    

    protected static ?string $navigationGroup = 'Administración de Personal';
    protected static ?int $navigationSort = 4;
    protected static ?string $navigationIcon = 'heroicon-o-user-group';


    protected static ?string $modelLabel = 'Oficina';
    protected static ?string $pluralModelLabel = 'Oficinas';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Datos de la Oficina')
                ->description('Registre el nombre del departamento, unidad u oficina dentro de la institución.')
                ->icon('heroicon-o-building-office')
                ->schema([
                    Forms\Components\TextInput::make('descripcion')
                        ->label('Nombre de la Oficina')
                        ->placeholder('Ej: Recursos Humanos, Contabilidad, Asuntos Administrativos...')
                        ->required()
                        ->maxLength(255)
                        ->columnSpanFull(), 
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('descripcion')
                    ->label('Oficina')
                    ->searchable()
                    ->sortable() 
                    ->weight('bold'), 
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(), 
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('descripcion', 'asc'); 
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
            'index' => Pages\ListOficinas::route('/'),
            'create' => Pages\CreateOficina::route('/create'),
            'edit' => Pages\EditOficina::route('/{record}/edit'),
        ];
    }
}