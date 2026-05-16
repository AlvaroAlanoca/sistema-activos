<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ServicioContratoResource\Pages;
use App\Filament\Resources\ServicioContratoResource\RelationManagers;
use App\Models\ServicioContrato;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Blade;
class ServicioContratoResource extends Resource
{
    protected static ?string $model = ServicioContrato::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
        protected static ?string $navigationGroup = 'Contratos';
        protected static ?int $navigationSort = 1;

public static function form(Form $form): Form
{
    return $form
        ->schema([
            Forms\Components\Section::make('Asignación de Responsable')
                ->schema([
                    // ESTE SELECT LLAMA A LA FUNCIÓN user()
                   Forms\Components\Select::make('id_user')
            ->label('Responsable a Cargo')
                ->relationship(
                name: 'user', 
                modifyQueryUsing: fn ($query) => $query->with('responsable')
                    )
                ->getOptionLabelFromRecordUsing(fn ($record) => $record->responsable ? $record->responsable->nombre_apellido : $record->name)
                ->default(\Illuminate\Support\Facades\Auth::id())
    
                // BLOQUEAMOS EL CAMPO PARA QUE NO SE PUEDA EDITAR
                ->disabled() 
    
                // Obligamos a Filament a enviar el dato oculto a MySQL
                ->dehydrated() 
                ->searchable()
                ->preload()
                ->required(),
                ]),

            Forms\Components\Section::make('Detalles del Servicio')
                ->schema([
                    // ESTE SELECT LLAMA A LA FUNCIÓN servicio()
                    Forms\Components\Select::make('id_servicio')
                        ->label('Servicio (CUCE / Empresa)')
                        ->relationship('servicio', 'cuce')
                        ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->cuce} - {$record->empresa}")
                        ->searchable()
                        ->preload()
                        ->required(),
                    
                    Forms\Components\Select::make('estado')
                        ->options([
                            'pendiente' => 'Pendiente',
                            'cumplido' => 'Cumplido',
                        ])
                        ->default('pendiente')
                        ->required(),
                ])->columns(2),

                Forms\Components\Textarea::make('descripcion') 
                    ->label('Descripción del Trabajo Realizado')
                    ->placeholder('Describa aquí las actividades, observaciones o avances del servicio...')
                    ->rows(4)
                    ->columnSpanFull(),

            Forms\Components\Section::make('Vigencia del Contrato')
                ->schema([
                    Forms\Components\DatePicker::make('fecha_inicio')
                        ->required(),
                    Forms\Components\DatePicker::make('fecha_fin')
                        ->required(),
                ])->columns(2),
        ]);
}

public static function table(Table $table): Table
{
    return $table
        ->columns([
            Tables\Columns\TextColumn::make('user.responsable.nombre_apellido')
    ->label('Responsable')
    ->searchable()
    ->sortable(),
                Tables\Columns\TextColumn::make('servicio.empresa')->label('Empresa')->sortable(),
            Tables\Columns\TextColumn::make('servicio.cuce')->label('CUCE')->searchable(),
            Tables\Columns\TextColumn::make('fecha_fin')->label('Vence el')->date()->sortable(),
            Tables\Columns\BadgeColumn::make('estado')
                ->colors([
                    'warning' => 'pendiente',
                    'success' => 'cumplido',
                ]),
        ])
        ->actions([
            Tables\Actions\EditAction::make(),
            
            // ACCIÓN PARA DESCARGAR PDF
            Tables\Actions\Action::make('pdf')
                ->label('Descargar Reporte')
                ->color('success')
                ->icon('heroicon-o-document-arrow-down')
            ->action(function (ServicioContrato $record) {
                    // Quitamos el .blade del nombre de la vista
                    $pdf = Pdf::loadView('reports.servicio_contrato', [
                        'contrato' => $record,
                    ]);
                    
                    return response()->streamDownload(function () use ($pdf) {
                        echo $pdf->stream();
                    }, "Reporte_Contrato_{$record->idservicio_contrato}.pdf");
                }),
        ])
        ->filters([
            // REPORTE: Esto permite al admin filtrar y ver solo pendientes o cumplidos
            Tables\Filters\SelectFilter::make('estado')
                ->options([
                    'pendiente' => 'Pendiente',
                    'cumplido' => 'Cumplido',
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
            'index' => Pages\ListServicioContratos::route('/'),
            'create' => Pages\CreateServicioContrato::route('/create'),
            'edit' => Pages\EditServicioContrato::route('/{record}/edit'),
        ];
    }
}
