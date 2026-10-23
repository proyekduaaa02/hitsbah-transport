<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $table = 'settings';

    protected $fillable = ['key', 'value'];

    /** Cache per-request agar tidak query berulang. */
    protected static ?array $cache = null;

    public static function semua(): array
    {
        return self::$cache ??= static::query()->pluck('value', 'key')->all();
    }

    public static function ambil(string $key, mixed $default = null): mixed
    {
        return self::semua()[$key] ?? $default;
    }

    public static function simpan(string $key, ?string $value): void
    {
        static::query()->updateOrCreate(['key' => $key], ['value' => $value]);
        self::$cache = null;
    }

    public static function lupakanCache(): void
    {
        self::$cache = null;
    }
}
