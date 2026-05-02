<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static bool $shouldRegisterNavigation = false;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')->required(),
Forms\Components\TextInput::make('email')->email()->required(),
Forms\Components\TextInput::make('password')->password()->required()->dehydrateStateUsing(fn ($state) => bcrypt($state))->hiddenOn('edit'),

// El selector de Rol
Forms\Components\Select::make('rol')
    ->options([
        'admin' => 'Administrador',
        'responsable' => 'Responsable de Activos',
    ])
    ->required()
    ->reactive(), // Hace que el formulario reaccione al cambio

// El selector para conectarlo con la tabla Responsables
Forms\Components\Select::make('id_responsable')
    ->label('Vincular con ficha de Responsable')
    ->relationship('responsable', 'nombre_apellido')
    ->searchable()
    // Solo mostramos este campo si el rol elegido es 'responsable'
    ->visible(fn (\Filament\Forms\Get $get) => $get('rol') === 'responsable')
    ->required(fn (\Filament\Forms\Get $get) => $get('rol') === 'responsable'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
