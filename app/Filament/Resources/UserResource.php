<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Permission\Models\Role; // ¡IMPORTANTE! Agregamos el modelo de Roles

class UserResource extends Resource
{
    use HasRoles;
    protected static ?string $model = User::class;

    
    protected static ?string $navigationIcon = 'heroicon-o-users'; // Cambié el ícono para que tenga más sentido (usuarios)
    protected static bool $shouldRegisterNavigation = true;
    protected static ?string $navigationLabel = 'Usuarios del Sistema';
    protected static ?string $navigationGroup = 'Seguridad';
    
    // Opcional: puedes meterlo en un grupo si quieres
    // protected static ?string $navigationGroup = 'Administración de Personal';

public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // 2. EL SELECTOR DE RESPONSABLE (Ahora siempre visible)
                Forms\Components\Select::make('id_responsable')
                    ->label('Vincular con ficha de Responsable')
                    ->relationship('responsable', 'nombre_apellido')
                    ->searchable()
                    ->preload()
                    ->required(), // Opcional: Quita esta línea si quieres que crear usuarios sin ficha sea permitido
                Forms\Components\TextInput::make('name')
                    ->label('Alias')
                    ->required(),
                    
                Forms\Components\TextInput::make('email')
                    ->label('Correo Electrónico')
                    ->email()
                    ->required(),
                    
                Forms\Components\TextInput::make('password')
                    ->label('Contraseña')
                    ->password()
                    ->dehydrateStateUsing(fn ($state) => bcrypt($state))
                    ->dehydrated(fn ($state) => filled($state)) 
                    ->required(fn (string $context): bool => $context === 'create'), 

                // 1. EL SELECTOR DE ROLES DE SHIELD
                Forms\Components\Select::make('roles')
                    ->label('Asignar Rol(es) de Acceso')
                    ->relationship('roles', 'name')
                    ->multiple() 
                    ->preload()
                    ->searchable(), // Ya no necesitamos ->live() porque nada depende de este campo ahora


            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Alias')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('email')
                    ->label('Correo')
                    ->searchable(),

                // Mostramos los roles como etiquetas (badges)
                Tables\Columns\TextColumn::make('roles.name')
                    ->label('Rol(es) en el Sistema')
                    ->badge()
                    ->color('primary')
                    ->searchable(),

                // Mostramos a quién está vinculado (si aplica)
                Tables\Columns\TextColumn::make('responsable.nombre_apellido')
                    ->label('Ficha Vinculada')
                    ->default('N/A')
                    ->color('gray'),
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