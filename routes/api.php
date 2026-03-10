<?php

declare(strict_types=1);

use Fitodac\LaravelMercadoPago\Http\Controllers\Api\CustomerController;
use Fitodac\LaravelMercadoPago\Http\Controllers\Api\HealthController;
use Fitodac\LaravelMercadoPago\Http\Controllers\Api\PaymentController;
use Fitodac\LaravelMercadoPago\Http\Controllers\Api\PaymentMethodController;
use Fitodac\LaravelMercadoPago\Http\Controllers\Api\PreferenceController;
use Fitodac\LaravelMercadoPago\Http\Controllers\Api\TestUserController;
use Fitodac\LaravelMercadoPago\Http\Controllers\Api\WebhookController;
use Illuminate\Support\Facades\Route;

Route::prefix((string) config('mercadopago.route_prefix', 'api/mercadopago'))
    ->name('mercadopago.')
    ->group(function (): void {
        Route::post('/webhooks', [WebhookController::class, 'store'])->name('webhooks.store');

        Route::middleware('mercadopago.demo')->group(function (): void {
            Route::get('/health', HealthController::class)->name('health');
            Route::get('/payment-methods', [PaymentMethodController::class, 'index'])->name('payment-methods.index');
            Route::post('/preferences', [PreferenceController::class, 'store'])->name('preferences.store');
            Route::post('/payments', [PaymentController::class, 'store'])->name('payments.store');
            Route::get('/payments/{paymentId}', [PaymentController::class, 'show'])->name('payments.show');
            Route::post('/payments/{paymentId}/refunds', [PaymentController::class, 'refund'])->name('payments.refund');
            Route::post('/customers', [CustomerController::class, 'store'])->name('customers.store');
            Route::get('/customers/{customerId}', [CustomerController::class, 'show'])->name('customers.show');
            Route::post('/customers/{customerId}/cards', [CustomerController::class, 'storeCard'])->name('customers.cards.store');
            Route::delete('/customers/{customerId}/cards/{cardId}', [CustomerController::class, 'destroyCard'])->name('customers.cards.destroy');
            Route::post('/test-users', [TestUserController::class, 'store'])->name('test-users.store');
        });
    });
