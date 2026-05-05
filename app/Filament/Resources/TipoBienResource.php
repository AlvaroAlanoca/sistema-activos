<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TipoBienResource\Pages;
use App\Models\TipoBien;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class TipoBienResource extends Resource
{
    protected static ?string $model = TipoBien::class;
    protected static ?string $navigationGroup = 'Gestión de Inventario';
    protected static ?int $navigationSort = 2;
    

    protected static ?string $modelLabel = 'Tipo de Bien';
    protected static ?string $pluralModelLabel = 'Tipos de Bien';
    protected static ?string $navigationLabel = 'Tipos de Bien';


    protected static ?string $navigationIcon = 'heroicon-o-rectangle-group'; 

    public static function form(Form $form): Form
    {
        return $form->schema([

            Forms\Components\Section::make('Clasificación del Bien')
                ->description('Defina el nombre de la categoría y asócielo a su respectiva partida presupuestaria.')
                ->icon('heroicon-o-tag')
                ->schema([
                    Forms\Components\TextInput::make('descripcion')
                        ->label('Nombre de la Categoría (Tipo de Bien)')
                        ->placeholder('Ej: Sillas Giratorias, Monitores, Escritorios...')
                        ->required()
                        ->maxLength(255)
                        ->columnSpan(1),
                    
                    
                    Forms\Components\Select::make('id_rubro')
                        ->label('Rubro Presupuestario')
                        ->relationship('rubro', 'idrubros')
                        ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->idrubros} - {$record->descripcion}")
                        ->searchable() 
                        ->preload()    
                        ->required()
                        ->columnSpan(1),
                ])->columns(2), 
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('descripcion')
                    ->label('Tipo de Bien')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'), // Ponemos el título en negrita

                
                Tables\Columns\TextColumn::make('rubro.clasificador_presupuestario')
                    ->label('Clasificador')
                    ->badge()
                    ->color('info')
                    ->sortable(),

                Tables\Columns\TextColumn::make('rubro.descripcion')
                    ->label('Descripción del Rubro')
                    ->searchable()
                    ->color('gray')
                    ->wrap()
                    ->sortable(),
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
        

        return $user && $user->rol === 'admin';
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