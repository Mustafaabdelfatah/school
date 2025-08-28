<?php

namespace App\Pipelines\Admin;

use App\Pipelines\Filter;
use Closure;
use Illuminate\Database\Eloquent\Builder;

class NameFilter extends Filter
{
    public function __construct(
        protected ?string $name
    ) {}

    public function handle(Builder $query, Closure $next): Builder
    {
        if ($this->hasValue($this->name)) {
            $query->where('name', 'LIKE', '%' . $this->name . '%');
        }

        return $next($query);
    }
}
