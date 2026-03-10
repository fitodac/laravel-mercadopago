<?php

declare(strict_types=1);

namespace Fitodac\LaravelMercadoPago;

use Fitodac\LaravelMercadoPago\Contracts\CredentialResolverInterface;
use Fitodac\LaravelMercadoPago\Http\Middleware\EnsureDemoRoutesEnabled;
use Fitodac\LaravelMercadoPago\Services\CardService;
use Fitodac\LaravelMercadoPago\Services\CustomerService;
use Fitodac\LaravelMercadoPago\Services\PaymentMethodService;
use Fitodac\LaravelMercadoPago\Services\PaymentService;
use Fitodac\LaravelMercadoPago\Services\PreferenceService;
use Fitodac\LaravelMercadoPago\Services\RefundService;
use Fitodac\LaravelMercadoPago\Services\TestUserService;
use Fitodac\LaravelMercadoPago\Services\WebhookService;
use Fitodac\LaravelMercadoPago\Support\EnvCredentialResolver;
use Fitodac\LaravelMercadoPago\Support\MercadoPagoClientFactory;
use Fitodac\LaravelMercadoPago\Support\SdkHttpClient;
use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;

final class LaravelMercadoPagoServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/mercadopago.php', 'mercadopago');

        $this->app->bind(CredentialResolverInterface::class, EnvCredentialResolver::class);
        $this->app->singleton(MercadoPagoClientFactory::class);
        $this->app->singleton(SdkHttpClient::class);
        $this->app->singleton(PaymentMethodService::class);
        $this->app->singleton(PreferenceService::class);
        $this->app->singleton(PaymentService::class);
        $this->app->singleton(RefundService::class);
        $this->app->singleton(CustomerService::class);
        $this->app->singleton(CardService::class);
        $this->app->singleton(WebhookService::class);
        $this->app->singleton(TestUserService::class);
    }

    public function boot(Router $router): void
    {
        $this->publishes([
            __DIR__ . '/../config/mercadopago.php' => config_path('mercadopago.php'),
        ], 'mercadopago-config');

        $router->aliasMiddleware('mercadopago.demo', EnsureDemoRoutesEnabled::class);

        $this->loadRoutesFrom(__DIR__ . '/../routes/api.php');
    }
}
