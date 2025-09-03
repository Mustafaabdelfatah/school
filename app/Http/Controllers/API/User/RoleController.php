<?php

namespace App\Http\Controllers\API\User;

use App\Filters\Global\JsonDisplayNameFilter;
use App\Filters\Global\OrderByFilter;
use App\Http\Controllers\Controller;
use App\Http\Requests\Global\Other\DeleteAllRequest;
use App\Http\Requests\Global\Other\PageRequest;
use App\Http\Requests\User\RoleRequest;
use App\Http\Resources\User\RoleResource;
use App\Models\Role;
use Illuminate\Http\JsonResponse;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use function __;

class RoleController extends Controller
{
    /**
     * @param PageRequest $request
     * @return JsonResponse
     */
    public function index(PageRequest $request): JsonResponse
    {
        $roles = app(Pipeline::class)
            ->send(Role::related()->with('permissions'))
            ->through([JsonDisplayNameFilter::class, OrderByFilter::class])
            ->thenReturn();

        return successResponse(fetchData($roles, $request->pageSize, RoleResource::class));
    }

    /**
     * @param RoleRequest $request
     * @return JsonResponse
     */
    public function store(RoleRequest $request): JsonResponse
    {
        return DB::transaction(function () use ($request) {
            $role = Role::create($request->validated());
            $role->syncPermissions($request->permissions);

            return createdResponse(new RoleResource($role->load('permissions')), __('api.created_success'));
        });
    }

    /**
     * @param Role $role
     * @return JsonResponse
     */
    public function show(Role $role): JsonResponse
    {
        return successResponse(new RoleResource($role->load('permissions')));
    }

    /**
     * @param RoleRequest $request
     * @param Role $role
     * @return JsonResponse
     */
    public function update(RoleRequest $request, Role $role): JsonResponse
    {
        return DB::transaction(function () use ($role, $request) {
            $role->update($request->validated());
            $role->syncPermissions($request->permissions);

            return updatedResponse(new RoleResource($role->refresh()->load('permissions')), __('api.updated_success'));
        });
    }

    /**
     * @param Role $role
     * @return JsonResponse
     */
    public function destroy(Role $role): JsonResponse
    {
        if ($role->roleUsers()->exists()) {
            return errorResponse(__('api.cant_delete'), null, 409);
        }

        $role->permissions()->detach();
        $role->deleteQuietly();

        return deletedResponse(__('api.deleted_success'));
    }
}