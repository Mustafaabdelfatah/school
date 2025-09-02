<?php

namespace App\Http\Controllers\API\Global\Setting;

use App\Filters\Setting\GroupFilter;
use App\Filters\Setting\KeyFilter;
use App\Http\Controllers\Controller;
use App\Http\Requests\Global\Setting\SettingRequest;
use App\Http\Requests\Global\Setting\TestCredentialsRequest;
use App\Http\Resources\Global\Setting\SettingResource;
use App\Mail\BasicMail;
use App\Models\Setting;
use App\Services\Global\UploadService;
use Illuminate\Http\JsonResponse;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Mail;
use Spatie\Permission\Middleware\PermissionMiddleware;

class SettingController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware(PermissionMiddleware::using('read-setting'), only: ['index']),
            new Middleware(PermissionMiddleware::using('update-setting'), only: ['setConfigForUser']),
        ];
    }

    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $query = app(Pipeline::class)
            ->send(Setting::query())
            ->through([KeyFilter::class, GroupFilter::class])
            ->thenReturn();

        $settings = $query->get()->groupBy('group');

        // Transform each setting into a resource
        $settingsResource = $settings->map(function ($group) {
            return SettingResource::collection($group);
        });

        return successResponse($settingsResource);
    }

    /**
     * @return JsonResponse
     */
    public function publicSetting(): JsonResponse
    {
        $query = app(Pipeline::class)
            ->send(Setting::query()->public())
            ->through([KeyFilter::class])
            ->thenReturn();

        $settings = $query->get()->groupBy('group');

        // Transform each setting into a resource
        $settingsResource = $settings->map(function ($group) {
            return SettingResource::collection($group);
        });

        return successResponse($settingsResource);
    }

    /**
     * @param SettingRequest $request
     * @return JsonResponse
     */
    public function setConfigForUser(SettingRequest $request): JsonResponse
    {
        foreach ($request->settings as $item) {
            $value = !empty($item['value']) ? $item['value'] : null;

            if ($value && is_file($value)) {
                $value = UploadService::store($item['value'], 'settings');
            }

            $setting = Setting::updateOrCreate([
                'key' => $item['key'],
                'group' => $item['group']
            ], [
                'value' => $value
            ]);

            if ($setting->is_env) {
                updateDotEnv([strtoupper($item['key']) => $value]);
            }
        }

        return successResponse(msg: __('api.updated_success'));
    }

    /**
     * @param TestCredentialsRequest $request
     * @return JsonResponse
     */
    public function testMailCredentials(TestCredentialsRequest $request): JsonResponse
    {
        Mail::to($request->email)->send(new BasicMail(null, [
            'title' => 'test_credentials',
            'mailMsg' => $request->body
        ]));

        return successResponse(msg: __('api.test_credentials_success'));
    }
}
