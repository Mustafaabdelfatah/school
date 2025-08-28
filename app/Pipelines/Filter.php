<?php

namespace App\Pipelines;

use Closure;
use Illuminate\Database\Eloquent\Builder;

abstract class Filter
{
    abstract public function handle(Builder $query, Closure $next): Builder;

    protected function hasValue($value): bool
    {
        return !is_null($value) && $value !== '' && $value !== [];
    }
}
