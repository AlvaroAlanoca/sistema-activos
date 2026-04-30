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

class RubroResource extends Resource
{
    protected static ?string $model = Rubro::class;
    protected static ?string $navigationGroup = 'Gestión de Inventario';
    protected static ?int $navigationSort = 3;

    protected static ?string $navigationIcon = 'heroicon-o-archive-box';

    public static function form(Form $form): Form
    {
        return $form->schema([
        Forms\Components\TextInput::make('clasificador_presupuestario')->required(),
        Forms\Components\TextInput::make('descripcion')->required(),
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRubros::route('/'),
            'create' => Pages\CreateRubro::route('/create'),
            'edit' => Pages\EditRubro::route('/{record}/edit'),
        ];
    }
}
