<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;
class MenuTransferenciasWidget extends Widget
{
    protected static string $view = 'filament.widgets.menu-transferencias-widget';

    protected int | string | array $columnSpan = 'full'; 
    
    
    public static function canView(): bool
    {
         /** @var \App\Models\User|null $user */
        $user = Auth::user();

        return $user && $user->hasAnyRole(['admin', 'super_admin']);
    }

}
