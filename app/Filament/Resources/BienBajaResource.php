<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BienBajaResource\Pages;
use App\Filament\Resources\BienBajaResource\RelationManagers;
use App\Models\BienBaja;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BienBajaResource extends Resource
{
    protected static ?string $model = BienBaja::class;
    protected static ?string $modelLabel = 'Baja de Activo';
    protected static ?string $pluralModelLabel = 'Bienes dados de Baja';
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

public static function form(Form $form): Form
    {
        return $form
            ->schema([
                \Filament\Forms\Components\Section::make('Detalles de la Baja')
                    ->description('Seleccione el activo y registre los motivos de su baja institucional.')
                    ->schema([
                        // Selector inteligente del Bien
\Filament\Forms\Components\Select::make('idbienes')
    ->label('Bien / Activo a Dar de Baja')
    ->relationship(
        name: 'bien', 
        titleAttribute: 'descripcion',
        // ¡ESTA ES LA LÍNEA CLAVE! Filtramos directamente en la base de datos
        modifyQueryUsing: fn (\Illuminate\Database\Eloquent\Builder $query) => $query->where('estado', 'DISPONIBLE')
    )
    ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->codigo} - {$record->descripcion}")
    ->searchable()
    ->preload()
    ->required()
    ->columnSpanFull(),

                        \Filament\Forms\Components\TextInput::make('motivo_baja')
                            ->label('Motivo de la Baja (Ej: Obsolescencia, Robo, Daño irreparable)')
                            ->required()
                            ->maxLength(255),

                        \Filament\Forms\Components\DatePicker::make('fecha_aprobacion')
                            ->label('Fecha de Aprobación')
                            ->default(now())
                            ->native(false)
                            ->required(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('bien.codigo')
                    ->label('Código Activo')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('bien.tipoBien.descripcion')
                    ->label('Categoría')
                    ->searchable()
                    ->limit(40),
                Tables\Columns\TextColumn::make('bien.descripcion')
                    ->label('Descripción del Bien')
                    ->searchable()
                    ->limit(40),
                Tables\Columns\TextColumn::make('bien.costo')
                    ->label('Costo')
                    ->searchable()
                    ->limit(40),

                Tables\Columns\TextColumn::make('motivo_baja')
                    ->label('Motivo Baja')
                    ->searchable()
                    ->color('danger'), // Color rojo para resaltar que es una baja

                Tables\Columns\TextColumn::make('fecha_aprobacion')
                    ->label('Aprobado el')
                    ->date('d/m/Y')
                    ->sortable(),
            ])
            ->defaultSort('fecha_aprobacion', 'desc'); // Ordenar por los más recientes
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
            'index' => Pages\ListBienBajas::route('/'),
            'create' => Pages\CreateBienBaja::route('/create'),
            'edit' => Pages\EditBienBaja::route('/{record}/edit'),
        ];
    }
}
