<?php

namespace App\Support;

use InvalidArgumentException;

final class FerryTariff
{
    public const DEFAULT_CODE = 'penumpang_dewasa';

    public static function defaultRouteKey(): string
    {
        return (string) config(
            'ferry_tariffs.default_route',
            'siwa_lasusua'
        );
    }

    public static function route(): array
    {
        return (array) config(
            'ferry_tariffs.routes.' . self::defaultRouteKey(),
            []
        );
    }

    public static function all(): array
    {
        $result = [];

        foreach ((array) (self::route()['items'] ?? []) as $item) {
            if (! is_array($item)) {
                continue;
            }

            $code = trim((string) ($item['code'] ?? ''));

            if ($code === '') {
                continue;
            }

            $result[$code] = $item;
        }

        return $result;
    }

    public static function codes(): array
    {
        return array_keys(self::all());
    }

    public static function options(): array
    {
        $options = [];

        foreach (self::all() as $code => $item) {
            $group = (string) ($item['group'] ?? 'Tarif');
            $label = (string) ($item['label'] ?? $code);
            $price = max((int) ($item['price'] ?? 0), 0);

            $options[$code] = sprintf(
                '%s — %s — %s',
                $group,
                $label,
                self::rupiah($price)
            );
        }

        return $options;
    }

    public static function normalize(?string $code): string
    {
        $code = trim((string) $code);
        $items = self::all();

        if ($code !== '' && array_key_exists($code, $items)) {
            return $code;
        }

        if (array_key_exists(self::DEFAULT_CODE, $items)) {
            return self::DEFAULT_CODE;
        }

        $firstCode = array_key_first($items);

        if ($firstCode === null) {
            throw new InvalidArgumentException(
                'Konfigurasi tarif ferry belum tersedia.'
            );
        }

        return (string) $firstCode;
    }

    public static function item(?string $code): array
    {
        $normalized = self::normalize($code);
        $item = self::all()[$normalized] ?? null;

        if (! is_array($item)) {
            throw new InvalidArgumentException(
                'Jenis tarif ferry tidak ditemukan.'
            );
        }

        return [
            'code' => $normalized,
            'group' => (string) ($item['group'] ?? 'Tarif'),
            'label' => (string) ($item['label'] ?? $normalized),
            'description' => (string) (
                $item['description']
                ?? $item['label']
                ?? $normalized
            ),
            'unit' => (string) ($item['unit'] ?? 'unit'),
            'price' => max((int) ($item['price'] ?? 0), 0),
        ];
    }

    public static function calculate(
        ?string $code,
        mixed $quantity
    ): array {
        $item = self::item($code);
        $quantity = max((int) $quantity, 1);

        return [
            'jenis_tarif' => $item['code'],
            'tarif_label' => $item['description'],
            'tarif_group' => $item['group'],
            'satuan' => $item['unit'],
            'harga_satuan' => $item['price'],
            'jumlah_tiket' => $quantity,
            'total_harga' => $item['price'] * $quantity,
        ];
    }

    public static function rupiah(mixed $value): string
    {
        return 'Rp' . number_format(
            (float) $value,
            0,
            ',',
            '.'
        );
    }

    public static function routeName(): string
    {
        return (string) (
            self::route()['label']
            ?? 'Siwa - Lasusua'
        );
    }
}
