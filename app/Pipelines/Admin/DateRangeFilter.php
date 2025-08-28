<?php

namespace App\Pipelines\Admin;

use App\Pipelines\Filter;
use Closure;
use Illuminate\Database\Eloquent\Builder;

class DateRangeFilter extends Filter
{
    public function __construct(
        protected ?string $dateFrom,
        protected ?string $dateTo
    ) {}

    public function handle(Builder $query, Closure $next): Builder
    {
        if ($this->hasValue($this->dateFrom)) {
            $query->whereDate('created_at', '>=', $this->dateFrom);
        }

        if ($this->hasValue($this->dateTo)) {
            $query->whereDate('created_at', '<=', $this->dateTo);
        }

        return $next($query);
    }
}
