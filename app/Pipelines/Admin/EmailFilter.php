<?php

namespace App\Pipelines\Admin;

use App\Pipelines\Filter;
use Closure;
use Illuminate\Database\Eloquent\Builder;

class EmailFilter extends Filter
{
    public function __construct(
        protected ?string $email
    ) {}

    public function handle(Builder $query, Closure $next): Builder
    {
        if ($this->hasValue($this->email)) {
            $query->where('email', 'LIKE', '%' . $this->email . '%');
        }

        return $next($query);
    }
}
