<?php

namespace App\Http\Controllers\API\User;

use App\Filters\Global\OrderByFilter;
use App\Filters\User\UserFilter;
use App\Http\Controllers\Controller;
use App\Http\Requests\Global\Other\DeleteAllRequest;
use App\Http\Requests\Global\Other\PageRequest;
use App\Http\Requests\User\UserRequest;
use App\Http\Resources\User\UserResource;
use App\Models\Role;
use App\Models\User;
use App\Services\Global\UploadService;
use Illuminate\Http\JsonResponse;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class UserController extends Controller
{
    /**
     * @param PageRequest $request
     * @return JsonResponse
     */
    public function index(PageRequest $request): JsonResponse
    {
        $query = app(Pipeline::class)
            ->send(User::with('roles'))
            ->through([UserFilter::class, OrderByFilter::class])
            ->thenReturn();

        return successResponse(fetchData($query, $request->pageSize, UserResource::class));
    }

    /**
     * @param UserRequest $request
     * @return JsonResponse
     */
    public function store(UserRequest $request): JsonResponse
    {
        return DB::transaction(function () use ($request) {
            $user = User::create($this->prepareData($request));
            $this->syncRelations($user, $request);

            return createdResponse(new UserResource($user->load('roles')), __('api.created_success'));
        });
    }

    /**
     * @param User $user
     * @return JsonResponse
     */
    public function show(User $user): JsonResponse
    {
        return successResponse(new UserResource($user->load('roles')));
    }

    /**
     * @param UserRequest $request
     * @param User $user
     * @return JsonResponse
     */
    public function update(UserRequest $request, User $user): JsonResponse
    {
        return DB::transaction(function () use ($user, $request) {
            $user->update($this->prepareData($request));
            $this->syncRelations($user, $request);

            return updatedResponse(new UserResource($user->refresh()->load('roles')), __('api.updated_success'));
        });
    }

    /**
     * @param User $user
     * @return JsonResponse
     */
    public function destroy(User $user): JsonResponse
    {
        UploadService::delete($user->avatar);
        $user->delete();

        return deletedResponse(__('api.deleted_success'));
    }


    /*
    |--------------------------------------------------------------------------
    | Helper Methods
    |--------------------------------------------------------------------------
    */
    /**
     * @param User $user
     * @param UserRequest $request
     * @return void
     */
    private function syncRelations(User $user, UserRequest $request): void
    {
        when($request->filled('roles'), static fn() => $user->syncRoles(Role::whereId($request->roles)->pluck('name')));
        when($request->filled('permissions'), static fn() => $user->syncPermissions($request->permissions));
    }

    /**
     * Prepare user data for storing or updating.
     */
    private function prepareData(UserRequest $request): array
    {
        $data = Arr::except($request->validated(), ['avatar', 'permissions', 'roles']);
        $data['avatar'] = UploadService::store($request->avatar, 'users');

        return $data;
    }
}
