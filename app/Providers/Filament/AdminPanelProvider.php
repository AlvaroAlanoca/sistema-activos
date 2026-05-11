<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login(\App\Filament\Pages\Auth\CustomLogin::class)
            ->brandName('Control de Activos Fijos - SEDUCA')

            ->brandLogo(fn () => new \Illuminate\Support\HtmlString('
            <div style="display: flex; align-items: center; gap: 12px;">
                <img src="' . asset('img/logo.png') . '" alt="Escudo SEDUCA" style="height: 3rem;">
                <span style="font-size: 1.5rem; font-weight: bold; letter-spacing: 1px; color: inherit;">SEDUCA</span>
            </div>
        '))
        ->brandLogoHeight('5rem')
        
            ->favicon(asset('img/logo.png'))
            ->colors([
            'primary' => \Filament\Support\Colors\Color::Blue,
            'gray' => \Filament\Support\Colors\Color::Slate,            
            ])
                        ->plugins([
            \BezhanSalleh\FilamentShield\FilamentShieldPlugin::make(),
        ])
            ->navigationGroups([
    'Transacciones',
    'Reportes',
    'Gestión de Inventario',
    'Administración de Personal',
    'Filament Shield',
])
            ->sidebarCollapsibleOnDesktop()            
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
->pages([
    // Reemplaza la ruta original por la tuya:
    \App\Filament\Pages\Dashboard::class,
])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                //Widgets\FilamentInfoWidget::class,
            ])
            // 3. Inyectar CSS solo en la página de Login
            ->renderHook(
                \Filament\View\PanelsRenderHook::AUTH_LOGIN_FORM_BEFORE,
                fn (): string => \Illuminate\Support\Facades\Blade::render('
                    <style>
                        /* Fondo azul degradado corporativo */
                        body {
                            background-image: linear-gradient(135deg, #0f172a 0%, #1e3a8a 100%) !important;
                            background-attachment: fixed;
                        }
                        
                        /* Le da sombra y borde elegante a la tarjeta blanca */
                        .fi-simple-main-ctn > div {
                            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5) !important;
                            border-radius: 1rem !important;
                        }

                        /* Centra el logo a la fuerza para que se vea simétrico */
                        .fi-logo {
                            justify-content: center !important;
                        }
                    </style>
                '),
            )
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->plugins([
                FilamentShieldPlugin::make(),
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
