<?php

namespace App\Http\Controllers\API\Setting;

use App\Http\Controllers\Controller;
use App\Http\Requests\Setting\UpdateSiteSettingRequest;
use App\Http\Resources\Setting\SiteSettingResource;
use App\Models\SiteSetting;
use App\Services\Setting\SiteSettingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SiteSettingController extends Controller
{
    protected SiteSettingService $siteSettingService;

    public function __construct(SiteSettingService $siteSettingService)
    {
        $this->siteSettingService = $siteSettingService;
    }

    /**
     * Display all site settings grouped by category
     */
    public function index(): JsonResponse
    {
        try {
            $settings = $this->siteSettingService->getAllSettings();

            return successResponse(
                $settings,
                __('api.settings_retrieved_successfully')
            );
        } catch (\Exception $e) {
            return errorResponse(__('api.failed_to_retrieve_settings'), null, 500);
        }
    }

    /**
     * Update site settings
     */
    public function update(UpdateSiteSettingRequest $request): JsonResponse
    {
        try {
            $settings = $this->siteSettingService->updateSettings($request->validated());

            return successResponse(
                $settings,
                __('api.settings_updated_successfully')
            );
        } catch (\Exception $e) {
            return errorResponse(__('api.failed_to_update_settings'), null, 500);
        }
    }

    /**
     * Get settings by group
     */
    public function getByGroup(string $group): JsonResponse
    {
        try {
            $settings = $this->siteSettingService->getSettingsByGroup($group);

            return successResponse(
                $settings,
                __('api.settings_retrieved_successfully')
            );
        } catch (\Exception $e) {
            return errorResponse(__('api.failed_to_retrieve_settings'), null, 500);
        }
    }

    /**
     * Get public settings for frontend
     */
    public function getPublicSettings(): JsonResponse
    {
        try {
            $settings = $this->siteSettingService->getPublicSettings();

            return successResponse(
                $settings,
                __('api.settings_retrieved_successfully')
            );
        } catch (\Exception $e) {
            return errorResponse(__('api.failed_to_retrieve_settings'), null, 500);
        }
    }
}
