<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\User\IndexRequest;
use App\Http\Requests\Admin\User\StoreRequest;
use App\Http\Requests\Admin\User\UpdateRequest;
use App\Http\Resources\UserResource;
use App\Contracts\Repository\UserRepositoryInterface;
use App\Pipelines\Admin\NameFilter;
use App\Pipelines\Admin\EmailFilter;
use App\Pipelines\Admin\StatusFilter;
use App\Pipelines\Admin\DateRangeFilter;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Pipeline\Pipeline;

class UserController extends Controller
{
    public function __construct(
        protected UserRepositoryInterface $userRepository
    ) {}

    public function index(IndexRequest $request): JsonResponse
    {
        $query = User::query();

        $users = app(Pipeline::class)
            ->send($query)
            ->through([
                new NameFilter($request->input('name')),
                new EmailFilter($request->input('email')),
                new StatusFilter($request->input('status')),
                new DateRangeFilter($request->input('date_from'), $request->input('date_to')),
            ])
            ->thenReturn()
            ->paginate($request->input('per_page', 15));

        return paginatedResponse($users, 'Users retrieved successfully');
    }

    public function store(StoreRequest $request): JsonResponse
    {
        $user = $this->userRepository->create($request->validated());

        return createdResponse(new UserResource($user), 'User created successfully');
    }

    public function show(User $user): JsonResponse
    {
        return successResponse(new UserResource($user), 'User retrieved successfully');
    }

    public function update(UpdateRequest $request, User $user): JsonResponse
    {
        $updatedUser = $this->userRepository->update($user, $request->validated());

        return updatedResponse(new UserResource($updatedUser), 'User updated successfully');
    }

    public function destroy(User $user): JsonResponse
    {
        $this->userRepository->delete($user);

        return deletedResponse('User deleted successfully');
    }
}
