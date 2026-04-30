<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OficinaResource\Pages;
use App\Filament\Resources\OficinaResource\RelationManagers;
use App\Models\Oficina;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OficinaResource extends Resource
{
    protected static ?string $model = Oficina::class;
    protected static ?string $navigationGroup = 'Administración de Personal';
    protected static ?int $navigationSort = 4;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    public static function form(Form $form): Form
    {
        return $form->schema([
        Forms\Components\TextInput::make('descripcion')->label('Nombre de la Oficina')->required(),
    ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
            
            Tables\Columns\TextColumn::make('descripcion')->label('Oficina')->searchable(),

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
            'index' => Pages\ListOficinas::route('/'),
            'create' => Pages\CreateOficina::route('/create'),
            'edit' => Pages\EditOficina::route('/{record}/edit'),
        ];
    }
}
