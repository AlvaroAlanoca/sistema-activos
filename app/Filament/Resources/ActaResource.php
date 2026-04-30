<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ActaResource\Pages;
use App\Models\Acta;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Barryvdh\DomPDF\Facade\Pdf;

class ActaResource extends Resource
{
    protected static ?string $model = Acta::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-duplicate';
    protected static ?string $navigationLabel = 'Gestión de Actas';
    protected static ?string $navigationGroup = 'Transacciones';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Información del Acta')
                ->description('Registre una nueva entrega de activos.')
                ->schema([
                    // Ocultamos el campo y forzamos a que siempre sea una ENTREGA
                    Forms\Components\Hidden::make('tipo')
                        ->default('ENTREGA'),

                    Forms\Components\TextInput::make('numero_acta')
                        ->label('Número de Acta')
                        ->placeholder('Ej: ACT-001/2026')
                        ->required()
                        ->unique(ignoreRecord: true),

                    Forms\Components\Select::make('id_responsables')
                        ->label('Responsable / Funcionario')
                        ->relationship('responsable', 'nombre_apellido')
                        ->searchable()
                        ->preload()
                        ->required(),
                ])->columns(2), // Reducido a 2 columnas porque quitamos el campo "tipo" visual

            Forms\Components\Section::make('Detalle de Bienes')
                ->description('Añada los bienes disponibles que entregará en esta acta.')
                ->schema([
                    Forms\Components\Repeater::make('items')
                        ->relationship('items')
                        ->schema([
                            Forms\Components\Select::make('id_bienes')
                                ->label('Seleccionar Bien')
                                ->relationship(
                                    name: 'bien', 
                                    titleAttribute: 'descripcion',
                                    // Búsqueda simple: Solo mostramos bienes DISPONIBLES
                                    modifyQueryUsing: fn (Builder $query) => $query->where('estado', 'DISPONIBLE')
                                )
                                ->searchable(['codigo', 'descripcion'])
                                ->getOptionLabelFromRecordUsing(fn ($record) => "[{$record->codigo}] - {$record->descripcion}")
                                ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                                ->required()
                                ->columnSpan(2),

                            Forms\Components\Select::make('estado')
                                ->label('Estado Físico')
                                ->options([
                                    'Bueno' => 'Bueno',
                                    'Regular' => 'Regular',
                                    'Malo' => 'Malo',
                                ])
                                ->required()
                                ->columnSpan(1),
                        ])
                        ->columns(3)
                        ->addActionLabel('Agregar Bien al Acta')
                        ->reorderable(false)
                        ->itemLabel(fn (array $state): ?string => $state['id_bienes'] ?? 'Nuevo Ítem'),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('numero_acta')
                    ->label('Nº de Acta')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('tipo')
                    ->label('Tipo')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'ENTREGA' => 'success',
                        'DEVOLUCION' => 'warning', // Aunque no se creen aquí, podrías ver las generadas automáticamente
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('responsable.nombre_apellido')
                    ->label('Responsable')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha Registro')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('tipo')
                    ->options([
                        'ENTREGA' => 'Entregas',
                        'DEVOLUCION' => 'Devoluciones',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                
                Tables\Actions\Action::make('pdf')
                    ->label('Descargar PDF')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('danger')
                    ->action(function (Acta $record) {
                        return response()->streamDownload(function () use ($record) {
                            echo Pdf::loadView('pdf.acta', [
                                'acta' => $record,
                                'items' => $record->items()->with('bien')->get(),
                            ])->output();
                        }, "Acta_{$record->numero_acta}.pdf");
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

public static function getPages(): array
{
    return [
        'index' => Pages\ListActas::route('/'),
        'create' => Pages\CreateActa::route('/create'),
        'edit' => Pages\EditActa::route('/{record}/edit'), // <--- Así es como lo entiende Filament
    ];
}
}