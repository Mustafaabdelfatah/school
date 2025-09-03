<?php

namespace App\Http\Controllers\API\Navigation;

use App\Http\Controllers\Controller;
use App\Http\Requests\Navigation\StoreNavigationRequest;
use App\Http\Requests\Navigation\UpdateNavigationRequest;
use App\Http\Resources\Navigation\NavigationResource;
use App\Http\Resources\Navigation\NavigationCollection;
use App\Models\Navigation;
use App\Services\Navigation\NavigationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NavigationController extends Controller
{
    protected NavigationService $navigationService;

    public function __construct(NavigationService $navigationService)
    {
        $this->navigationService = $navigationService;
    }

    /**
     * Display a listing of navigation items
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $navigations = $this->navigationService->getAllNavigations($request->all());

            return successResponse(
                new NavigationCollection($navigations),
                __('api.navigations_retrieved_successfully')
            );
        } catch (\Exception $e) {
            return errorResponse(__('api.failed_to_retrieve_navigations'), null, 500);
        }
    }

    /**
     * Store a newly created navigation item
     */
    public function store(StoreNavigationRequest $request): JsonResponse
    {
        try {
            $navigation = $this->navigationService->createNavigation($request->validated());

            return successResponse(
                new NavigationResource($navigation),
                __('api.navigation_created_successfully'),
                201
            );
        } catch (\Exception $e) {
            return errorResponse(__('api.failed_to_create_navigation'), null, 500);
        }
    }

    /**
     * Display the specified navigation item
     */
    public function show(Navigation $navigation): JsonResponse
    {
        try {
            $navigation = $this->navigationService->getNavigationWithRelations($navigation);

            return successResponse(
                new NavigationResource($navigation),
                __('api.navigation_retrieved_successfully')
            );
        } catch (\Exception $e) {
            return errorResponse(__('api.failed_to_retrieve_navigation'), null, 500);
        }
    }

    /**
     * Update the specified navigation item
     */
    public function update(UpdateNavigationRequest $request, Navigation $navigation): JsonResponse
    {
        try {
            $updatedNavigation = $this->navigationService->updateNavigation($navigation, $request->validated());

            return successResponse(
                new NavigationResource($updatedNavigation),
                __('api.navigation_updated_successfully')
            );
        } catch (\Exception $e) {
            return errorResponse(__('api.failed_to_update_navigation'), null, 500);
        }
    }

    /**
     * Remove the specified navigation item
     */
    public function destroy(Navigation $navigation): JsonResponse
    {
        try {
            $this->navigationService->deleteNavigation($navigation);

            return successResponse(
                null,
                __('api.navigation_deleted_successfully')
            );
        } catch (\Exception $e) {
            return errorResponse(__('api.failed_to_delete_navigation'), null, 500);
        }
    }

    /**
     * Get navigation by location
     */
    public function getByLocation(string $location): JsonResponse
    {
        try {
            $navigation = $this->navigationService->getNavigationByLocation($location);

            return successResponse(
                new NavigationCollection($navigation),
                __('api.navigation_retrieved_successfully')
            );
        } catch (\Exception $e) {
            return errorResponse(__('api.failed_to_retrieve_navigation'), null, 500);
        }
    }
}
