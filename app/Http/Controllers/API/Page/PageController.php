<?php

namespace App\Http\Controllers\API\Page;

use App\Http\Controllers\Controller;
use App\Http\Requests\Page\StorePageRequest;
use App\Http\Requests\Page\UpdatePageRequest;
use App\Http\Resources\Page\PageResource;
use App\Http\Resources\Page\PageCollection;
use App\Models\Page;
use App\Services\Page\PageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PageController extends Controller
{
    protected PageService $pageService;

    public function __construct(PageService $pageService)
    {
        $this->pageService = $pageService;
    }

    /**
     * Display a listing of pages
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $pages = $this->pageService->getAllPages($request->all());

            return successResponse(
                new PageCollection($pages),
                __('api.pages_retrieved_successfully')
            );
        } catch (\Exception $e) {
            return errorResponse(__('api.failed_to_retrieve_pages'), null, 500);
        }
    }

    /**
     * Store a newly created page
     */
    public function store(StorePageRequest $request): JsonResponse
    {
        try {
            $page = $this->pageService->createPage($request->validated());

            return successResponse(
                new PageResource($page),
                __('api.page_created_successfully'),
                201
            );
        } catch (\Exception $e) {
            return errorResponse(__('api.failed_to_create_page'), null, 500);
        }
    }

    /**
     * Display the specified page
     */
    public function show(Page $page): JsonResponse
    {
        try {
            $page = $this->pageService->getPageWithRelations($page);

            return successResponse(
                new PageResource($page),
                __('api.page_retrieved_successfully')
            );
        } catch (\Exception $e) {
            return errorResponse(__('api.failed_to_retrieve_page'), null, 500);
        }
    }

    /**
     * Update the specified page
     */
    public function update(UpdatePageRequest $request, Page $page): JsonResponse
    {
        try {
            $updatedPage = $this->pageService->updatePage($page, $request->validated());

            return successResponse(
                new PageResource($updatedPage),
                __('api.page_updated_successfully')
            );
        } catch (\Exception $e) {
            return errorResponse(__('api.failed_to_update_page'), null, 500);
        }
    }

    /**
     * Remove the specified page
     */
    public function destroy(Page $page): JsonResponse
    {
        try {
            $this->pageService->deletePage($page);

            return successResponse(
                null,
                __('api.page_deleted_successfully')
            );
        } catch (\Exception $e) {
            return errorResponse(__('api.failed_to_delete_page'), null, 500);
        }
    }

    /**
     * Get page by slug for frontend
     */
    public function getBySlug(Request $request, string $slug): JsonResponse
    {
        try {
            $page = $this->pageService->getPageBySlug($slug);

            if (!$page) {
                return notFoundResponse(__('api.page_not_found'));
            }

            return successResponse(
                new PageResource($page),
                __('api.page_retrieved_successfully')
            );
        } catch (\Exception $e) {
            return errorResponse(__('api.failed_to_retrieve_page'), null, 500);
        }
    }

    /**
     * Get navigation structure
     */
    public function getNavigation(): JsonResponse
    {
        try {
            $navigation = $this->pageService->getNavigationStructure();

            return successResponse(
                $navigation,
                __('api.navigation_retrieved_successfully')
            );
        } catch (\Exception $e) {
            return errorResponse(__('api.failed_to_retrieve_navigation'), null, 500);
        }
    }
}
