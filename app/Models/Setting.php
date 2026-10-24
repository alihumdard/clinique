<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

/**
 * Site settings, read far more often than written. The whole table is
 * cached as one entry and the cache is dropped on any write, so a page
 * render costs no queries for them.
 */
class Setting extends Model
{
    protected $primaryKey = 'key';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['key', 'value', 'type', 'group'];

    private const CACHE_KEY = 'settings.all';

    protected static function booted(): void
    {
        static::saved(fn () => self::flushCache());
        static::deleted(fn () => self::flushCache());
    }

    /**
     * Drops the cached set.
     *
     * The events above cover every write through a model. A mass update or
     * delete — $query->delete() — fires no model events at all, so code
     * that writes that way has to call this itself.
     */
    public static function flushCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    public static function all($columns = ['*'])
    {
        return parent::all($columns);
    }

    /** @return array<string, mixed> */
    public static function allValues(): array
    {
        return Cache::rememberForever(self::CACHE_KEY, function () {
            return static::query()->get()->mapWithKeys(fn (self $s) => [
                $s->key => $s->castValue(),
            ])->all();
        });
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        return self::allValues()[$key] ?? $default;
    }

    public static function put(string $key, mixed $value, string $type = 'string', string $group = 'general'): void
    {
        static::updateOrCreate(
            ['key' => $key],
            [
                'value' => $type === 'json' ? json_encode($value) : (string) $value,
                'type' => $type,
                'group' => $group,
            ]
        );
    }

    protected function castValue(): mixed
    {
        return match ($this->type) {
            'boolean' => filter_var($this->value, FILTER_VALIDATE_BOOLEAN),
            'integer' => (int) $this->value,
            'json' => json_decode($this->value, true),
            default => $this->value,
        };
    }
}
