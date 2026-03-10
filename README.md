# Laravel Mercado Pago

Paquete reusable para integrar Mercado Pago en proyectos Laravel 12 mediante servicios desacoplados, auto-discovery de Laravel y un set de endpoints JSON de apoyo para desarrollo y pruebas.

El paquete registra:

- Configuración propia en `config/mercadopago.php`
- Un `ServiceProvider` con auto-discovery
- Servicios para `preferences`, `payments`, `refunds`, `customers`, `cards`, `payment methods`, `test users` y `webhooks`
- Rutas JSON bajo un prefijo configurable

## Alcance real del paquete

Este paquete hoy soporta proyectos con:

- PHP `^8.2`
- Laravel / componentes `illuminate/*` `^12.0`
- SDK oficial `mercadopago/dx-php` `^3.8`

No incluye:

- migraciones
- vistas Blade
- componentes frontend
- persistencia propia de pagos
- colas o jobs preconfigurados

Su responsabilidad es encapsular la comunicación con el SDK de Mercado Pago y exponer una API base reutilizable.

## Qué resuelve

- Resolver credenciales desde configuración y variables de entorno
- Configurar el SDK con `access token` y `runtime environment`
- Exponer servicios listos para inyección por contenedor
- Registrar un webhook con validación de firma HMAC cuando existe secreto configurado
- Exponer rutas demo para pruebas locales y de testing

## Instalación en cualquier proyecto Laravel

### Opción A: paquete local por `path repository`

Es la forma más directa cuando el paquete vive dentro del mismo repositorio o en un directorio vecino.

1. Copiá o cloná el paquete dentro de tu proyecto Laravel, por ejemplo:

```text
my-app/
├── app/
├── config/
├── plugins/
│   └── laravel-mercadopago/
└── composer.json
```

2. Agregá el repositorio local a `composer.json` del proyecto host:

```json
{
  "repositories": [
    {
      "type": "path",
      "url": "plugins/laravel-mercadopago",
      "options": {
        "symlink": true
      }
    }
  ]
}
```

3. Instalá el paquete:

```bash
composer require fitodac/laravel-mercadopago:^1.0
```

4. Verificá el auto-discovery:

```bash
php artisan package:discover
php artisan route:list --name=mercadopago
```

### Opción B: paquete versionado

Si tu proyecto ya tiene acceso al paquete desde un repositorio Composer privado, VCS o registry interno, instalalo con:

```bash
composer require fitodac/laravel-mercadopago:^1.0
```

En ese caso no necesitás la sección `repositories` por `path`.

## Activación y configuración

El paquete registra automáticamente el provider:

```php
Fitodac\LaravelMercadoPago\LaravelMercadoPagoServiceProvider::class
```

Opcionalmente podés publicar el archivo de configuración:

```bash
php artisan vendor:publish --tag=mercadopago-config
```

Variables disponibles:

```ini
MERCADOPAGO_ACCESS_TOKEN=
MERCADOPAGO_PUBLIC_KEY=
MERCADOPAGO_WEBHOOK_SECRET=
MERCADOPAGO_ROUTE_PREFIX=api/mercadopago
MERCADOPAGO_ENABLE_DEMO_ROUTES=true
MERCADOPAGO_RUNTIME_ENVIRONMENT=
```

También podés indicar una clase propia para resolver credenciales:

```php
'credential_resolver' => App\Support\MercadoPago\StoreCredentialResolver::class,
```

Esa clase debe implementar `Fitodac\LaravelMercadoPago\Contracts\CredentialResolverInterface`. Con eso el paquete puede seguir usando variables de entorno para un solo comercio o resolver claves desde base de datos para escenarios multi-store.

### Qué hace cada variable

| Variable                          | Requerida                          | Descripción                                                                                                  |
| --------------------------------- | ---------------------------------- | ------------------------------------------------------------------------------------------------------------ |
| `MERCADOPAGO_ACCESS_TOKEN`        | Sí                                 | Token usado por el SDK para llamar a Mercado Pago                                                            |
| `MERCADOPAGO_PUBLIC_KEY`          | No                                 | Clave pública útil para integraciones frontend                                                               |
| `MERCADOPAGO_WEBHOOK_SECRET`      | No, pero recomendada en producción | Permite validar la firma del webhook                                                                         |
| `MERCADOPAGO_ROUTE_PREFIX`        | No                                 | Prefijo de rutas. Por defecto `api/mercadopago`                                                              |
| `MERCADOPAGO_ENABLE_DEMO_ROUTES`  | No                                 | Habilita endpoints demo en `local` y `testing`                                                               |
| `MERCADOPAGO_RUNTIME_ENVIRONMENT` | No                                 | Fuerza el runtime del SDK. Si no se define, el paquete usa `local` en `local/testing` y `server` en el resto |

### Recomendaciones de seguridad

- Nunca subas credenciales reales al repositorio.
- Nunca compartas `access tokens`, `public keys` privadas de backoffice ni secretos de webhook.
- En producción configurá `MERCADOPAGO_WEBHOOK_SECRET`.
- Si vas a exponer operaciones de cobro a usuarios reales, preferí controladores propios con autenticación y autorización de tu aplicación. Las rutas demo son de apoyo, no una capa completa de negocio.

## Rutas que registra el paquete

Todas las rutas usan el prefijo configurado en `MERCADOPAGO_ROUTE_PREFIX`.

### Ruta siempre activa

| Método | Ruta                        | Uso                                                      |
| ------ | --------------------------- | -------------------------------------------------------- |
| `POST` | `/api/mercadopago/webhooks` | Recepción y validación de notificaciones de Mercado Pago |

### Rutas demo

Estas rutas solo responden cuando:

- `MERCADOPAGO_ENABLE_DEMO_ROUTES=true`
- `app()->environment()` es `local` o `testing`

En cualquier otro caso responden `404`.

| Método   | Ruta                                                     | Servicio asociado                                 |
| -------- | -------------------------------------------------------- | ------------------------------------------------- |
| `GET`    | `/api/mercadopago/health`                                | Verifica si el paquete tiene credenciales mínimas |
| `GET`    | `/api/mercadopago/payment-methods`                       | `PaymentMethodService`                            |
| `POST`   | `/api/mercadopago/preferences`                           | `PreferenceService`                               |
| `POST`   | `/api/mercadopago/payments`                              | `PaymentService`                                  |
| `GET`    | `/api/mercadopago/payments/{paymentId}`                  | `PaymentService`                                  |
| `POST`   | `/api/mercadopago/payments/{paymentId}/refunds`          | `RefundService`                                   |
| `POST`   | `/api/mercadopago/customers`                             | `CustomerService`                                 |
| `GET`    | `/api/mercadopago/customers/{customerId}`                | `CustomerService`                                 |
| `POST`   | `/api/mercadopago/customers/{customerId}/cards`          | `CardService`                                     |
| `DELETE` | `/api/mercadopago/customers/{customerId}/cards/{cardId}` | `CardService`                                     |
| `POST`   | `/api/mercadopago/test-users`                            | `TestUserService`                                 |

## Implementación recomendada en tu proyecto

La forma más segura de usar el paquete en un proyecto real es inyectar sus servicios en tus propias acciones o controladores y dejar que tu aplicación maneje:

- autenticación
- autorización
- validación de reglas de negocio
- persistencia local
- logging y auditoría

### Ejemplo: crear una preferencia desde un controlador propio

```php
<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Fitodac\LaravelMercadoPago\Services\PreferenceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class CheckoutPreferenceController
{
    public function store(Request $request, PreferenceService $preferenceService): JsonResponse
    {
        $payload = $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*.title' => ['required', 'string'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
            'payer' => ['sometimes', 'array'],
            'back_urls' => ['sometimes', 'array'],
            'notification_url' => ['sometimes', 'url'],
            'external_reference' => ['sometimes', 'string'],
        ]);

        $preference = $preferenceService->create($payload);

        return response()->json([
            'id' => data_get($preference, 'id'),
            'init_point' => data_get($preference, 'init_point'),
            'sandbox_init_point' => data_get($preference, 'sandbox_init_point'),
        ], 201);
    }
}
```

### Ejemplo: crear un pago con tarjeta tokenizada

```php
<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Fitodac\LaravelMercadoPago\Services\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class MercadoPagoPaymentController
{
    public function store(Request $request, PaymentService $paymentService): JsonResponse
    {
        $payload = $request->validate([
            'transaction_amount' => ['required', 'numeric', 'min:0.01'],
            'token' => ['required', 'string'],
            'description' => ['required', 'string'],
            'installments' => ['required', 'integer', 'min:1'],
            'payment_method_id' => ['required', 'string'],
            'payer' => ['required', 'array'],
            'payer.email' => ['required', 'email'],
        ]);

        $payment = $paymentService->create($payload);

        return response()->json($payment, 201);
    }
}
```

### Ejemplo: consultar medios de pago

```php
use Fitodac\LaravelMercadoPago\Services\PaymentMethodService;

Route::get('/checkout/payment-methods', function (PaymentMethodService $service) {
    return response()->json($service->all());
});
```

## Payloads válidos de las rutas demo

Los endpoints del paquete validan estos campos mínimos.

### `POST /preferences`

```json
{
  "items": [
    {
      "title": "Producto demo",
      "quantity": 1,
      "unit_price": 100.5
    }
  ],
  "payer": {
    "email": "buyer@example.com"
  },
  "back_urls": {
    "success": "https://tu-app.test/pagos/exito",
    "pending": "https://tu-app.test/pagos/pendiente",
    "failure": "https://tu-app.test/pagos/error"
  },
  "notification_url": "https://tu-app.test/api/mercadopago/webhooks",
  "external_reference": "pedido-1001"
}
```

### `POST /payments`

```json
{
  "transaction_amount": 100.5,
  "token": "CARD_TOKEN",
  "description": "Pago pedido 1001",
  "installments": 1,
  "payment_method_id": "visa",
  "payer": {
    "email": "buyer@example.com"
  },
  "external_reference": "pedido-1001",
  "notification_url": "https://tu-app.test/api/mercadopago/webhooks"
}
```

### `POST /customers`

```json
{
  "email": "buyer@example.com",
  "first_name": "Ada",
  "last_name": "Lovelace"
}
```

### `POST /customers/{customerId}/cards`

```json
{
  "token": "CARD_TOKEN"
}
```

### `POST /payments/{paymentId}/refunds`

Reembolso total:

```json
{}
```

Reembolso parcial:

```json
{
  "amount": 50
}
```

### `POST /test-users`

```json
{
  "site_id": "MLA",
  "description": "Usuario de prueba para QA"
}
```

Sitios permitidos por el validador actual:

- `MLA`
- `MLB`
- `MLC`
- `MLM`
- `MLU`
- `MCO`
- `MPE`

## Flujo completo de integración recomendado

### 1. Instalar y configurar el paquete

```bash
composer require fitodac/laravel-mercadopago:^1.0
php artisan package:discover
```

Definí en `.env`:

```ini
MERCADOPAGO_ACCESS_TOKEN=tu_access_token
MERCADOPAGO_PUBLIC_KEY=tu_public_key
MERCADOPAGO_WEBHOOK_SECRET=tu_webhook_secret
MERCADOPAGO_ENABLE_DEMO_ROUTES=true
```

### 2. Verificar que Laravel registró las rutas

```bash
php artisan route:list --name=mercadopago
```

### 3. Probar la salud de configuración

```bash
curl http://localhost:8000/api/mercadopago/health
```

Respuesta esperada:

```json
{
  "ok": true,
  "data": {
    "configured": true,
    "has_public_key": true,
    "has_webhook_secret": true,
    "environment": "local"
  },
  "meta": []
}
```

### 4. Crear una preferencia

```bash
curl --request POST \
  --url http://localhost:8000/api/mercadopago/preferences \
  --header 'Content-Type: application/json' \
  --data '{
    "items": [
      {
        "title": "Producto demo",
        "quantity": 1,
        "unit_price": 100.5
      }
    ]
  }'
```

### 5. Integrar el frontend de checkout

Este paquete puede proveerte la preferencia o el pago desde backend, pero el frontend de checkout lo resolvés en tu aplicación consumiendo:

- `MERCADOPAGO_PUBLIC_KEY`
- el `id` de la preferencia o el token de pago que tu backend devuelva

El paquete no incluye widgets JS ni componentes UI. Esa capa queda en el proyecto host.

### 6. Procesar webhooks

El endpoint registrado por el paquete es:

```text
POST /api/mercadopago/webhooks
```

El servicio `WebhookService` devuelve:

- `acknowledged`
- `validated`
- `topic`
- `resource`
- `payload`

Si configuraste `MERCADOPAGO_WEBHOOK_SECRET` y el request trae `x-signature`, el paquete valida la firma usando:

- `data.id` del query string o del payload
- header `x-request-id`
- valor `ts` de `x-signature`

Si la firma no coincide, responde `401`.

### 7. Ejecutar tu lógica de negocio

El paquete valida y normaliza el contacto con el SDK, pero la actualización de órdenes, facturas, membresías o estados internos debe vivir en tu aplicación.

Una estrategia habitual es:

1. recibir el webhook
2. validar la firma
3. despachar un job propio
4. consultar el recurso real en Mercado Pago si necesitás consistencia fuerte
5. persistir el resultado en tu base de datos

## Pruebas locales

### Smoke checks mínimos

```bash
php artisan route:list --name=mercadopago
curl http://localhost:8000/api/mercadopago/health
curl http://localhost:8000/api/mercadopago/payment-methods
```

### Probar creación de preferencia

```bash
curl --request POST \
  --url http://localhost:8000/api/mercadopago/preferences \
  --header 'Content-Type: application/json' \
  --data '{
    "items": [
      {
        "title": "Producto demo",
        "quantity": 1,
        "unit_price": 100
      }
    ]
  }'
```

### Probar webhook localmente

1. Exponé tu entorno local con un túnel HTTPS.
2. Configurá en Mercado Pago la URL pública apuntando a `/api/mercadopago/webhooks`.
3. Verificá que `MERCADOPAGO_WEBHOOK_SECRET` coincida con el secreto configurado.
4. Revisá la respuesta JSON del endpoint y tus logs de aplicación.

### Probar con usuarios de prueba

El paquete expone:

```text
POST /api/mercadopago/test-users
```

Usalo para automatizar QA con credenciales de prueba de Mercado Pago. Si necesitás medios de pago o credenciales de sandbox, obtenelos desde tu cuenta y documentación oficial de Mercado Pago; no los guardes en el repositorio.

### Pruebas automatizadas presentes en este repositorio

Este workspace ya incluye tests para el paquete en:

- `plugins/laravel-mercadopago/tests/Unit`
- `plugins/laravel-mercadopago/tests/Feature`

Y están incorporados en el `phpunit.xml` del host. Podés ejecutarlos desde la raíz del proyecto host con:

```bash
php artisan test --filter=MercadoPago
```

Si consumís el paquete desde otro proyecto, replicá al menos:

- tests de creación de preferencia
- tests de creación de pago
- tests de tu flujo de webhook
- tests de autorización de tus controladores propios

## Despliegue en servidor

### Variables mínimas de producción

```ini
APP_ENV=production
MERCADOPAGO_ACCESS_TOKEN=...
MERCADOPAGO_PUBLIC_KEY=...
MERCADOPAGO_WEBHOOK_SECRET=...
MERCADOPAGO_ENABLE_DEMO_ROUTES=false
MERCADOPAGO_ROUTE_PREFIX=api/mercadopago
```

### Pasos recomendados

1. Instalar dependencias:

```bash
composer install --no-dev --optimize-autoloader
```

2. Confirmar que el paquete quedó instalado:

```bash
php artisan package:discover
php artisan route:list --name=mercadopago
```

3. Limpiar y reconstruir cachés:

```bash
php artisan config:clear
php artisan cache:clear
php artisan config:cache
php artisan route:cache
```

4. Validar la ruta pública del webhook:

```bash
curl --request POST \
  --url https://tu-dominio.com/api/mercadopago/webhooks \
  --header 'Content-Type: application/json' \
  --data '{"type":"payment","data":{"id":"123"}}'
```

Si el secreto no está configurado, el endpoint acepta el payload y devuelve `validated=false`. En producción no es la configuración recomendada.

### Checklist de producción

- `MERCADOPAGO_ENABLE_DEMO_ROUTES=false`
- `MERCADOPAGO_WEBHOOK_SECRET` configurado
- HTTPS activo
- logs centralizados
- monitoreo sobre errores `401`, `422` y `500`
- reintentos controlados en tu lógica de negocio, no en el controlador del paquete

## Respuestas y manejo de errores

Las rutas del paquete responden JSON.

### Respuesta exitosa

```json
{
  "ok": true,
  "data": {},
  "meta": []
}
```

### Respuesta de error

```json
{
  "ok": false,
  "message": "Mensaje del error"
}
```

Estados relevantes:

- `401` firma de webhook inválida
- `422` configuración incompleta del paquete
- `500` error inesperado

Si el SDK devuelve un error HTTP propio, el paquete propaga ese status.

## Troubleshooting

### `Mercado Pago access token is not configured.`

Definí `MERCADOPAGO_ACCESS_TOKEN` y luego limpiá caché de config:

```bash
php artisan config:clear
```

### `404` en rutas demo

Verificá estas dos condiciones:

- `MERCADOPAGO_ENABLE_DEMO_ROUTES=true`
- el entorno es `local` o `testing`

En `production` las rutas demo quedan deshabilitadas aunque la variable esté en `true`.

### El webhook responde `401`

Revisá:

- `MERCADOPAGO_WEBHOOK_SECRET`
- header `x-signature`
- header `x-request-id`
- `data.id` enviado en query string o payload

### El paquete está instalado pero no aparecen rutas

Ejecutá:

```bash
composer dump-autoload
php artisan package:discover
php artisan route:list --name=mercadopago
```

## Resumen operativo

Si querés usar este paquete con un enfoque mantenible:

1. instalalo por Composer
2. configurá credenciales por `.env`
3. usá servicios propios para tus casos de negocio
4. dejá las rutas demo solo para desarrollo y QA
5. protegé y monitoreá el webhook en producción
