<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\MenuItem;
use Filament\Navigation\NavigationItem;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
//use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
//use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

class TenantPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('tenant')                 // ✅ é um painel “tenant”, não “admin”
            ->path('tenant')               // ✅ suas rotas ficam em /tenant/*
            ->login()                      // cria /tenant/login
            ->authGuard('web')             // mesmo guard que você usou no Tinker
            ->databaseNotifications()
            ->databaseNotificationsPolling('30s')
            ->colors(['primary' => Color::Blue])

            ->discoverResources(app_path('Filament/Tenant/Resources'), 'App\\Filament\\Tenant\\Resources')
            ->resources([\App\Filament\Tenant\Resources\Users\UserResource::class])
            ->discoverPages(app_path('Filament/Tenant/Pages'), 'App\\Filament\\Tenant\\Pages')
            ->pages([Dashboard::class])
            ->discoverWidgets(app_path('Filament/Tenant/Widgets'), 'App\\Filament\\Tenant\\Widgets')
            ->widgets([AccountWidget::class, FilamentInfoWidget::class])

            ->userMenuItems([
                MenuItem::make()
                    ->label('Dashboard Principal')
                    ->url('/dashboard')
                    ->icon('heroicon-o-home'),
            ])
            ->navigationItems([
                NavigationItem::make('Voltar ao Dashboard')
                    ->url('/dashboard')
                    ->icon('heroicon-o-arrow-left-circle')
                    ->sort(-1)
                    ->group('Sistema'),
            ])

            // 🔑 Tenancy antes do stack web/Filament
            ->middleware([
                'web',

                \App\Http\Middleware\ConditionalTenancy::class,
                    //PreventAccessFromCentralDomains::class,
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
            ->authMiddleware([Authenticate::class])

            ->brandName('TriLinux')
            ->favicon(asset('favicon.ico'));
    }
}
