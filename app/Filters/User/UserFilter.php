<?php

namespace App\Filters\User;

use App\Filters\Trait\ActiveFilter;
use App\Filters\Trait\TrashedFilter;
use Closure;

class UserFilter
{

    public function handle($request, Closure $next)
    {
        $query = $next($request);

        $query->when(request()->has('search') && !empty(request('search')), function ($query) {
            $query->where(function ($query) {
                $query->where('name', 'like', '%' . request('search') . '%')
                    ->orWhere('email', 'like', '%' . request('search') . '%')
                    ->orWhere('phone', 'like', '%' . request('search') . '%');
            });
        });


        return $query;
    }
}