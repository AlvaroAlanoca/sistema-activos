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
use Filament\View\PanelsRenderHook;
use Illuminate\Support\Facades\Blade;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
        ->font('Inter') // Una fuente más moderna
        ->profile()
        ->colors([
        'primary' => \Filament\Support\Colors\Color::hex('#06b6d4'), // Turquesa
    // ... otros colores
])
->darkMode(false)
->renderHook(
    PanelsRenderHook::HEAD_END,
    fn (): string => Blade::render('
        <style>
            /* 1. ESTILOS GLOBALES (Los que ya teníamos) */
            .fi-btn { border-radius: 9999px !important; padding-left: 1.5rem !important; padding-right: 1.5rem !important; box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1); }
            .fi-section, .fi-ta-ctn { border-radius: 1.5rem !important; border: none !important; box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.05) !important; }
            .fi-header-heading { font-weight: 800 !important; text-transform: uppercase; letter-spacing: 0.025em; color: #1e293b; }

            /* ========================================= */
            /* 2. NUEVO: MAGIA PARA LA BARRA LATERAL     */
            /* ========================================= */
            
            /* Fondo de la barra y separación limpia */
            .fi-sidebar { 
                background-color: #ffffff !important; 
                border-right: none !important;
                box-shadow: 4px 0 20px rgba(0,0,0,0.03) !important; /* Sombra suave hacia la derecha */
            }

            /* Títulos de los grupos (Ej: "Transacciones", "Contratos") */
            .fi-sidebar-group-label {
                font-weight: 800 !important;
                color: #06b6d4 !important; /* Turquesa para que combinen con tu widget */
                text-transform: uppercase !important;
                letter-spacing: 0.1em !important;
                font-size: 0.75rem !important;
                margin-top: 1rem !important;
            }

            /* Forma de píldora para todas las opciones del menú */
            .fi-sidebar-item > a, 
            .fi-sidebar-item > button {
                border-radius: 9999px !important; /* Ultra redondeado */
                transition: all 0.3s ease !important;
                margin-bottom: 0.25rem !important;
            }

            /* Efecto al pasar el ratón (Hover) */
            .fi-sidebar-item > a:hover, 
            .fi-sidebar-item > button:hover {
                background-color: #f8fafc !important;
                transform: translateX(6px) !important; /* Se mueve un poquito a la derecha */
            }

            /* ESTADO ACTIVO: Cuando estás en esa pantalla */
            .fi-sidebar-item-active > a,
            .fi-sidebar-item-active > button {
                background-color: #06b6d4 !important; /* Fondo turquesa vibrante */
                color: #ffffff !important; /* Texto blanco */
                box-shadow: 0 4px 10px rgba(6, 182, 212, 0.4) !important; /* Resplandor (Glow) */
            }

            /* Ícono del estado activo en color blanco */
            .fi-sidebar-item-active .fi-icon {
                color: #ffffff !important;
            }
                /* ========================================= */
/* 3. AJUSTES DE ESPACIADO SUPERIOR          */
/* ========================================= */

/* Reduce el espacio entre la barra superior y el contenido de la página */
.fi-main-ctn {
    padding-top: 1.5rem !important; 
}

/* Quita el margen invisible que tiene el título por defecto */
.fi-header {
    margin-top: 0 !important;
    padding-top: 0 !important;
}

/* Opcional: Hacer el texto de la institución un poco más grande y de color corporativo */
.fi-header-heading {
    font-size: 1.8rem !important; 
    color: #0f172a !important; /* Azul muy oscuro/casi negro para máxima formalidad */
}
    .fi-main-ctn {
    padding-top: 0.25rem !important; 
}
    .fi-global-search {
    display: none !important;
}
/* ========================================= */
/* 5. ESTILO DE LA BARRA SUPERIOR (TOPBAR)   */
/* ========================================= */

/* Forzar el fondo en la cabecera y en el nav interno */
.fi-topbar,
.fi-topbar nav {
    background: linear-gradient(90deg, #06b6d4 0%, #06b6d4 100%) !important;
    background-color: #06b6d4 !important; /* Respaldo */
    border-bottom: none !important;
}

/* Sombras para separar del cuerpo */
.fi-topbar {
    box-shadow: 0 4px 15px -3px rgba(0, 0, 0, 0.1) !important;
}

/* Forzar que los íconos (hamburguesa, notificaciones) se vuelvan blancos */
.fi-topbar .fi-icon-btn, 
.fi-topbar .fi-topbar-item-icon {
    color: #ffffff !important;
}

/* Quitar fondos grises al pasar el ratón por los botones de la barra superior */
.fi-topbar .fi-icon-btn:hover {
    background-color: rgba(255, 255, 255, 0.1) !important;
}
    /* ========================================= */
/* 6. ESTILIZAR Y CENTRAR GRÁFICOS           */
/* ========================================= */

/* Contenedor del gráfico: le damos un ancho máximo y lo centramos */
.fi-wi-chart {
    max-width: 600px !important; /* Tamaño ideal para una dona */
    margin: 0 auto !important;   /* Centrado horizontal absoluto */
}

/* Estilizar la tarjeta que envuelve al gráfico */
.fi-wi-chart .fi-section {
    background-color: #ffffff !important;
    border-radius: 2rem !important;
    border: 1px solid #f1f5f9 !important;
    padding: 1rem !important;
}

/* Título del gráfico */
.fi-wi-chart .fi-section-header-heading {
    text-align: center !important;
    width: 100%;
    font-size: 1.1rem !important;
    color: #475569 !important;
    margin-bottom: 1rem;
}
    /* ========================================= */
/* 7. BOTONES DE ACCIÓN EN FORMULARIOS       */
/* ========================================= */

/* Contenedor inferior de los formularios */
.fi-form-actions {
    background-color: #f8fafc !important; /* Fondo sutil para enmarcar las acciones */
    padding: 1rem 1.5rem !important;
    border-radius: 1.25rem !important;
    border: 1px solid #e2e8f0 !important;
    margin-top: 1.75rem !important;
}

/* Configuración base para todos los botones del formulario (Formato Píldora) */
.fi-form-actions .fi-btn {
    border-radius: 9999px !important; 
    font-weight: 700 !important;
    text-transform: uppercase !important;
    letter-spacing: 0.05em !important;
    font-size: 0.75rem !important;
    padding-left: 1.5rem !important;
    padding-right: 1.5rem !important;
    transition: all 0.2s ease !important;
}

/* A. BOTÓN PRINCIPAL: "CREAR" / "GUARDAR" */
.fi-form-actions .fi-btn.fi-color-primary {
    background-color: #06b6d4 !important; /* Turquesa */
    color: #ffffff !important;
    box-shadow: 0 4px 12px -2px rgba(6, 182, 212, 0.4) !important;
}
.fi-form-actions .fi-btn.fi-color-primary:hover {
    background-color: #0891b2 !important;
    transform: translateY(-1px) !important;
    box-shadow: 0 6px 15px -2px rgba(6, 182, 212, 0.6) !important;
}

/* B. BOTÓN SECUNDARIO: "CREAR Y CREAR OTRO" */
/* Filament lo genera como un botón gris sin enlace. Lo capturamos y lo pintamos Slate */
.fi-form-actions .fi-btn.fi-color-gray:not([href]) {
    background-color: #475569 !important; /* Azul Slate */
    color: #ffffff !important;
    border: none !important;
    box-shadow: 0 4px 12px -2px rgba(71, 85, 105, 0.3) !important;
}
.fi-form-actions .fi-btn.fi-color-gray:not([href]):hover {
    background-color: #334155 !important;
    transform: translateY(-1px) !important;
    box-shadow: 0 6px 15px -2px rgba(71, 85, 105, 0.5) !important;
}

/* C. BOTÓN DE RETORNO: "CANCELAR" */
/* Filament lo renderiza como un enlace (etiqueta con atributo href) para volver atrás */
.fi-form-actions .fi-btn[href] {
    background-color: #f1f5f9 !important; /* Gris claro sutil */
    color: #64748b !important; /* Texto Slate suave */
    border: 1px solid #cbd5e1 !important;
}
.fi-form-actions .fi-btn[href]:hover {
    background-color: #e2e8f0 !important;
    color: #1e293b !important;
    border-color: #94a3b8 !important;
}
/* ========================================= */
/* 8. BOTONES DE CABECERA (Listados)         */
/* ========================================= */

/* Atrapamos cualquier botón dentro de la cabecera de la página */
.fi-header .fi-btn,
.fi-header-actions .fi-btn,
.fi-actions .fi-btn.fi-color-primary {
    border-radius: 9999px !important; /* Forma de píldora */
    font-weight: 800 !important;
    text-transform: uppercase !important;
    letter-spacing: 0.05em !important;
    font-size: 0.75rem !important;
    padding-left: 1.5rem !important;
    padding-right: 1.5rem !important;
    background-color: #06b6d4 !important; /* Turquesa SEDUCA */
    color: #ffffff !important;
    box-shadow: 0 4px 12px -2px rgba(6, 182, 212, 0.4) !important;
    transition: all 0.2s ease !important;
    border: none !important;
}

/* Efecto al pasar el ratón */
.fi-header .fi-btn:hover,
.fi-header-actions .fi-btn:hover,
.fi-actions .fi-btn.fi-color-primary:hover {
    background-color: #0891b2 !important; /* Turquesa oscuro */
    transform: translateY(-2px) !important;
    box-shadow: 0 6px 15px -2px rgba(6, 182, 212, 0.6) !important;
}
        </style>
    ')
)
->renderHook(
    \Filament\View\PanelsRenderHook::USER_MENU_BEFORE,
    fn (): string => \Illuminate\Support\Facades\Blade::render('
        <div class="hidden sm:flex items-center gap-1 mr-4 text-xs text-white font-semibold tracking-wide uppercase">
            Bienvenido/a, <span class="font-extrabold ml-1">{{ auth()->user()->name }}</span>
        </div>
    ')
)
->renderHook(
    \Filament\View\PanelsRenderHook::TOPBAR_START,
    fn (): string => \Illuminate\Support\Facades\Blade::render('
        <div class="hidden md:flex items-center text-lg font-black text-white tracking-wider uppercase ml-4">
            DIRECCIÓN DEPARTAMENTAL DE EDUCACIÓN LA PAZ
        </div>
    ')
)
            ->default()
            ->id('admin')
            ->path('admin')
            ->login(\App\Filament\Pages\Auth\CustomLogin::class)
            ->brandName('CONTROL DE BIENES Y SERVICIOS DDELPZ')
            ->brandLogo(fn () => new \Illuminate\Support\HtmlString('
                <div style="display: flex; align-items: center; gap: 12px;">
                    <img src="' . asset('img/logo.png') . '" alt="Escudo DDELPZ" style="height: 3rem;">
                    <span style="font-size: 1.5rem; font-weight: bold; letter-spacing: 1px; color: inherit;">DDELPZ</span>
                </div>
            '))
            ->brandLogoHeight('5rem')
            ->favicon(asset('img/logo.png'))
            ->colors([
                'primary' => \Filament\Support\Colors\Color::Blue,
                'gray' => \Filament\Support\Colors\Color::Slate,            
            ])
            // PLUGIN REGISTRADO UNA SOLA VEZ:
            ->plugins([
                FilamentShieldPlugin::make(),
            ])
            ->navigationGroups([
                'Transacciones',
                'Contratos',
                'Reportes',
                'Gestión de Inventario',
                'Administración de Personal',
                'Seguridad',
            ])
            ->sidebarCollapsibleOnDesktop()            
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                \App\Filament\Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                //Widgets\AccountWidget::class,
            ])
            ->renderHook(
                \Filament\View\PanelsRenderHook::AUTH_LOGIN_FORM_BEFORE,
                fn (): string => \Illuminate\Support\Facades\Blade::render('
                    <style>
                        body { background-image: linear-gradient(135deg, #0f172a 0%, #1e3a8a 100%) !important; background-attachment: fixed; }
                        .fi-simple-main-ctn > div { box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5) !important; border-radius: 1rem !important; }
                        .fi-logo { justify-content: center !important; }
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
            ->authMiddleware([
                Authenticate::class,
            ]);

            
    }
}