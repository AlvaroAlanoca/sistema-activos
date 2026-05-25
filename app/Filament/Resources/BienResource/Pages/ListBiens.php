<?php

namespace App\Filament\Resources\BienResource\Pages;

use App\Filament\Resources\BienResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ListBiens extends ListRecords
{
    protected static string $resource = BienResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    // 👇 MAGIA DE INTERFAZ: Pestañas 👇
    public function getTabs(): array
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        // Solo mostramos estas pestañas si es el rol Responsable
        if ($user && $user->hasRole('responsable') && !$user->hasRole('admin') && !$user->hasRole('super_admin')) {
            return [
                'mis_activos' => Tab::make('Mis Activos en Custodia')
                    ->icon('heroicon-m-archive-box')
                    ->badgeColor('success')
                    ->modifyQueryUsing(function (Builder $query) use ($user) {
                        return $query->whereIn('idbienes', function ($subquery) use ($user) {
                            $subquery->select('ai.id_bienes')
                                     ->from('acta_items as ai')
                                     ->join('actas as a', 'a.idacta', '=', 'ai.id_acta')
                                     ->where('a.id_responsables', $user->responsable_id)
                                     ->where('a.tipo', '!=', 'DEVOLUCION')
                                     ->whereRaw('a.idacta = (SELECT MAX(a2.idacta) FROM acta_items as ai2 INNER JOIN actas as a2 ON a2.idacta = ai2.id_acta WHERE ai2.id_bienes = ai.id_bienes)');
                        });
                    }),
                
                'catalogo' => Tab::make('Catálogo Disponible')
                    ->icon('heroicon-m-magnifying-glass')
                    ->badgeColor('info')
                    ->modifyQueryUsing(fn (Builder $query) => $query->where('estado', 'DISPONIBLE')),
            ];
        }

        // Si es administrador, no mostramos pestañas (ve todo en una sola tabla)
        return [];
    }
}