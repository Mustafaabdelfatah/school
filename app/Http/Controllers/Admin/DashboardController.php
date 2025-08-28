<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\User\IndexRequest;
use App\Http\Requests\Admin\User\StoreRequest;
use App\Http\Requests\Admin\User\UpdateRequest;
use App\Contracts\Repository\UserRepositoryInterface;
use App\Pipelines\Admin\NameFilter;
use App\Pipelines\Admin\EmailFilter;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Pipeline\Pipeline;

class DashboardController extends Controller
{
    public function __construct(
        protected UserRepositoryInterface $userRepository
    ) {}

    public function index(): View
    {
        $usersCount = User::count();
        $recentUsers = User::latest()->take(5)->get();

        return view('admin.dashboard.index', compact('usersCount', 'recentUsers'));
    }
}
