<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OficinaCargoResource\Pages;
use App\Filament\Resources\OficinaCargoResource\RelationManagers;
use App\Models\OficinaCargo;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class OficinaCargoResource extends Resource
{
    protected static ?string $model = OficinaCargo::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationGroup = 'Administración de Personal';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form->schema([
        Forms\Components\Select::make('id_oficinas')
            ->relationship('oficina', 'descripcion')->required(),
        Forms\Components\Select::make('id_cargos')
            ->relationship('cargo', 'descripcion')->required(),
    ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
               Tables\Columns\TextColumn::make('oficina.descripcion')
                ->label('Oficina')
                ->searchable()
                ->sortable(),

            Tables\Columns\TextColumn::make('cargo.descripcion')
                ->label('Cargo')
                ->searchable()
                ->sortable(),
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
