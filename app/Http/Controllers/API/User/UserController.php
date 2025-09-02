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
        Gate::authorize('view', User::class);

        $query = app(Pipeline::class)
            ->send(User::with('roles')->related())
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
        Gate::authorize('create', User::class);

        return DB::transaction(function () use ($request) {
            $user = User::create($this->prepareData($request));
            $this->syncRelations($user, $request);

            return successResponse(new UserResource($user->load('roles')), __('api.created_success'));
        });
    }

    /**
     * @param User $user
     * @return JsonResponse
     */
    public function show(User $user): JsonResponse
    {
        Gate::authorize('view', $user);

        return successResponse(new UserResource($user->load('roles')));
    }

    /**
     * @param UserRequest $request
     * @param User $user
     * @return JsonResponse
     */
    public function update(UserRequest $request, User $user): JsonResponse
    {
        Gate::authorize('update', $user);

        return DB::transaction(function () use ($user, $request) {
            $user->update($this->prepareData($request));
            $this->syncRelations($user, $request);

            return successResponse(new UserResource($user->refresh()->load('roles')), __('api.updated_success'));
        });
    }

    /**
     * @param User $user
     * @return JsonResponse
     */
    public function destroy(User $user): JsonResponse
    {
        Gate::authorize('delete', $user);

        UploadService::delete($user->avatar);
        $user->delete();

        return successResponse(msg: __('api.deleted_success'));
    }

    /**
     * @param DeleteAllRequest $request
     * @return JsonResponse
     */
    public function destroyAll(DeleteAllRequest $request): JsonResponse
    {
        Gate::authorize('delete', User::class);

        User::whereIn('id', $request->ids)->delete();

        return successResponse(msg: __('api.deleted_success'));
    }

    /**
     * @param int $id
     * @return JsonResponse
     */

    public function restore(int $id): JsonResponse
    {
        Gate::authorize('restore', User::class);

        User::onlyTrashed()->findOrFail($id)->restore();

        return successResponse(msg: __('api.restored_success'));
    }

    /**
     * @param int $id
     * @return JsonResponse
     */
    public function forceDelete(int $id): JsonResponse
    {
        Gate::authorize('delete', User::class);

        User::onlyTrashed()->findOrFail($id)->forceDelete();

        return successResponse(msg: __('api.deleted_success'));
    }

    /**
     * @param User $user
     * @return JsonResponse
     */
    public function changeStatus(User $user): JsonResponse
    {
        $user->is_active = !$user->is_active;
        $user->save();

        return successResponse(msg: __('api.updated_success'));
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
        when($request->filled('locations'), static fn() => $user->locations()->sync($request->locations));
    }

    /**
     * Prepare user data for storing or updating.
     */
    private function prepareData(UserRequest $request): array
    {
        $data = Arr::except($request->validated(), ['avatar', 'permissions', 'roles', 'locations']);
        $data['avatar'] = UploadService::store($request->avatar, 'users');

        return $data;
    }
}
