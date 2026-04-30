<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TipoBienResource\Pages;
use App\Filament\Resources\TipoBienResource\RelationManagers;
use App\Models\TipoBien;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TipoBienResource extends Resource
{
    protected static ?string $model = TipoBien::class;
protected static ?string $navigationGroup = 'Gestión de Inventario';
protected static ?int $navigationSort = 2;
    protected static ?string $navigationLabel = 'Tipo Bien'; // Lo que sale en el menú izquierdo

    protected static ?string $navigationIcon = 'heroicon-o-archive-box';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('descripcion')
                ->required()
                ->maxLength(255),
            
            Forms\Components\Select::make('id_rubro')
                ->label('Rubro Presupuestario')
                ->relationship('rubro', 'clasificador_presupuestario')
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('descripcion')->searchable(),
                Tables\Columns\TextColumn::make('rubro.descripcion'),
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
            'index' => Pages\ListTipoBiens::route('/'),
            'create' => Pages\CreateTipoBien::route('/create'),
            'edit' => Pages\EditTipoBien::route('/{record}/edit'),
        ];
    }
}
