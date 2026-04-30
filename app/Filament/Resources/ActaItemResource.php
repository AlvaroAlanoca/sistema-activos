<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ActaItemResource\Pages;
use App\Filament\Resources\ActaItemResource\RelationManagers;
use App\Models\ActaItem;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ActaItemResource extends Resource
{
    protected static ?string $model = ActaItem::class;
    protected static ?string $navigationLabel = 'Historial de Movimientos'; // Un nombre más pro que "Acta Items"
    protected static ?string $navigationGroup = 'Transacciones';
    protected static ?int $navigationSort = 2;
    protected static ?string $navigationIcon = 'heroicon-o-document-duplicate';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('id_acta')
                ->relationship('acta', 'numero_acta')
                ->required(),
            Forms\Components\Select::make('id_bienes')
                ->relationship('bien', 'descripcion')
                ->required(),
            Forms\Components\TextInput::make('estado')
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('acta.numero_acta')->label('Acta'),
                Tables\Columns\TextColumn::make('bien.codigo')->label('Código'),
                Tables\Columns\TextColumn::make('bien.descripcion')->label('Bien'),
                Tables\Columns\TextColumn::make('bien.estado') // Mostramos el estado real del bien
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                    'DISPONIBLE' => 'success',
                    'ENTREGADO' => 'danger',
                    default => 'gray',
            }),
                      ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\Action::make('devolver')
                ->label('Registrar Devolución')
                ->icon('heroicon-o-arrow-path')
                ->color('success')
                ->requiresConfirmation()
                ->visible(fn ($record) => $record->bien->estado === 'ENTREGADO') // Solo si está entregado
                ->action(function ($record) {
                // 1. Cambiamos el estado del bien a DISPONIBLE
                $record->bien->update(['estado' => 'DISPONIBLE']);
                
                // 2. Notificación de éxito
                \Filament\Notifications\Notification::make()
                    ->title('Bien devuelto con éxito')
                    ->success()
                    ->send();
                    }),
            
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('devolucion_masiva')
            ->label('Devolución Masiva')
            ->icon('heroicon-o-check-badge')
            ->color('success')
            ->requiresConfirmation()
            ->action(function (\Illuminate\Database\Eloquent\Collection $records) {
                foreach ($records as $record) {
                    $record->bien->update(['estado' => 'DISPONIBLE']);
                }
            })
            ->deselectRecordsAfterCompletion(),
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
            'index' => Pages\ListActaItems::route('/'),
            'create' => Pages\CreateActaItem::route('/create'),
            'edit' => Pages\EditActaItem::route('/{record}/edit'),
        ];
    }
}
