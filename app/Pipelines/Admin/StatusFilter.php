<?php

namespace App\Pipelines\Admin;

use App\Pipelines\Filter;
use Closure;
use Illuminate\Database\Eloquent\Builder;

class StatusFilter extends Filter
{
    public function __construct(
        protected ?string $status
    ) {}

    public function handle(Builder $query, Closure $next): Builder
    {
        if ($this->hasValue($this->status)) {
            if ($this->status === 'verified') {
                $query->whereNotNull('email_verified_at');
            } elseif ($this->status === 'unverified') {
                $query->whereNull('email_verified_at');
            }
        }

        return $next($query);
    }
}
