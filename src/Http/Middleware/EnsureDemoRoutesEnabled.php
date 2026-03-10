<?php

declare(strict_types=1);

namespace Fitodac\LaravelMercadoPago\Http\Middleware;

use Closure;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final readonly class EnsureDemoRoutesEnabled
{
    public function __construct(
        private Repository $config,
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        if (
            ! $this->config->get('mercadopago.enable_demo_routes', true)
            || ! app()->environment(['local', 'testing'])
        ) {
            abort(404);
        }

        return $next($request);
    }
}
