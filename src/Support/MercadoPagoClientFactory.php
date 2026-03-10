<?php

declare(strict_types=1);

namespace Fitodac\LaravelMercadoPago\Support;

use Fitodac\LaravelMercadoPago\Contracts\CredentialResolverInterface;
use Fitodac\LaravelMercadoPago\Exceptions\MercadoPagoConfigurationException;
use Illuminate\Contracts\Config\Repository;

final readonly class MercadoPagoClientFactory
{
    public function __construct(
        private CredentialResolverInterface $credentialResolver,
        private Repository $config,
    ) {}

    public function makeFirstAvailable(array $clientClasses): object
    {
        $this->configureSdk();

        foreach ($clientClasses as $clientClass) {
            if (class_exists($clientClass)) {
                return new $clientClass();
            }
        }

        throw MercadoPagoConfigurationException::clientClassNotFound($clientClasses);
    }

    public function callFirstAvailable(
        object $client,
        array $methodNames,
        mixed ...$arguments,
    ): mixed {
        foreach ($methodNames as $methodName) {
            if (method_exists($client, $methodName)) {
                return $client->{$methodName}(...$arguments);
            }
        }

        throw MercadoPagoConfigurationException::clientMethodNotFound($client, $methodNames);
    }

    private function configureSdk(): void
    {
        $sdkConfigClass = 'MercadoPago\\MercadoPagoConfig';

        if (! class_exists($sdkConfigClass)) {
            throw MercadoPagoConfigurationException::sdkNotInstalled();
        }

        $credentials = $this->credentialResolver->resolve();

        $sdkConfigClass::setAccessToken($credentials->accessToken);

        $runtimeEnvironment = $this->config->get('mercadopago.runtime_environment');

        if (! is_string($runtimeEnvironment) || $runtimeEnvironment === '') {
            $runtimeEnvironment = app()->environment(['local', 'testing']) ? 'local' : 'server';
        }

        foreach (['setRuntimeEnvironment', 'setRuntimeEnviroment'] as $methodName) {
            if (method_exists($sdkConfigClass, $methodName)) {
                $sdkConfigClass::{$methodName}($runtimeEnvironment);
                break;
            }
        }
    }
}
