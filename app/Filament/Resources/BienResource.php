<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BienResource\Pages;
use App\Filament\Resources\BienResource\RelationManagers;
use App\Models\Bien;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BienResource extends Resource
{
    protected static ?string $model = Bien::class;
    protected static ?string $navigationGroup = 'Gestión de Inventario';
    protected static ?int $navigationSort = 1;
    protected static ?string $navigationLabel = 'Bienes';

    protected static ?string $navigationIcon = 'heroicon-o-archive-box';

    public static function form(Form $form): Form
    {
        return $form->schema([
        Forms\Components\TextInput::make('codigo')->required(),
        Forms\Components\TextInput::make('descripcion')->required(),
        Forms\Components\Select::make('estado')
        ->options([
        'DISPONIBLE' => 'Disponible',
        'ENTREGADO' => 'Entregado (En uso)',
        'MANTENIMIENTO' => 'En Mantenimiento',
    ])
    ->default('DISPONIBLE')
    ->required(),
        Forms\Components\Select::make('id_tipo_bien')
            ->relationship('tipoBien', 'descripcion')
            ->required(),
    ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('codigo')
                ->label('Código')
                ->searchable()
                ->sortable()
                ->copyable(), // Permite copiar el código con un clic

            Tables\Columns\TextColumn::make('descripcion')
                ->label('Descripción')
                ->searchable(),

            // Mostramos el Tipo de Bien usando la relación
            Tables\Columns\TextColumn::make('tipoBien.descripcion')
                ->label('Categoría')
                ->sortable(),

            // Mostramos el Rubro (saltando a través de TipoBien)
            Tables\Columns\TextColumn::make('tipoBien.rubro.descripcion')
                ->label('Rubro Presupuestario')
                ->color('gray'),

            Tables\Columns\TextColumn::make('estado')
            ->badge()
            ->color(fn (string $state): string => match ($state) {
            'DISPONIBLE' => 'success',
            'ENTREGADO' => 'danger',
            'MANTENIMIENTO' => 'warning',
            default => 'gray',
            }),
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
            'index' => Pages\ListBiens::route('/'),
            'create' => Pages\CreateBien::route('/create'),
            'edit' => Pages\EditBien::route('/{record}/edit'),
        ];
    }
}
