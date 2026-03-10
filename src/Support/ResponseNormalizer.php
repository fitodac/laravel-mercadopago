<?php

declare(strict_types=1);

namespace Fitodac\LaravelMercadoPago\Support;

use BackedEnum;
use JsonSerializable;
use UnitEnum;

final class ResponseNormalizer
{
    public static function normalize(mixed $value): mixed
    {
        if (is_array($value)) {
            return array_map(self::normalize(...), $value);
        }

        if ($value instanceof JsonSerializable) {
            return self::normalize($value->jsonSerialize());
        }

        if ($value instanceof BackedEnum) {
            return $value->value;
        }

        if ($value instanceof UnitEnum) {
            return $value->name;
        }

        if (! is_object($value)) {
            return $value;
        }

        $publicProperties = get_object_vars($value);

        if ($publicProperties !== []) {
            return array_map(self::normalize(...), $publicProperties);
        }

        $normalized = [];

        foreach ((array) $value as $key => $item) {
            $normalized[self::cleanObjectKey($key)] = self::normalize($item);
        }

        return $normalized;
    }

    private static function cleanObjectKey(string $key): string
    {
        return preg_replace('/^\x00.+\x00/', '', $key) ?? $key;
    }
}
