<?php

namespace Xgrz\PayNow\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Amount implements CastsAttributes
{
    public function get(Model $model, string $key, mixed $value, array $attributes): int|float
    {
        return $value / 100;
    }

    public function set(Model $model, string $key, mixed $value, array $attributes): int
    {
        return Str::replace(',', '.', $value) * 100;
    }
}
