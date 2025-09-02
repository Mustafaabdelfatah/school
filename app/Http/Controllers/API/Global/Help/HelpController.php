<?php

namespace App\Http\Controllers\API\Global\Help;

use App\Http\Controllers\Controller;
use App\Http\Requests\Global\Help\HelpEnumRequest;
use App\Http\Requests\Global\Help\HelpModelRequest;
use App\Services\Global\HelpService;
use Illuminate\Http\JsonResponse;

class HelpController extends Controller
{
    public function __construct(private readonly  HelpService $helpService)
    {
    }

    /**
     * Retrieves and transforms data from specified models based on the provided request.
     *
     * @param HelpModelRequest $request
     * @return JsonResponse
     */
    public function models(HelpModelRequest $request): JsonResponse
    {
        $result = $this->helpService->getModels($request);

        return successResponse($result);
    }

    /**
     * Retrieves a list of enums based on the request parameters.
     *
     * @param HelpEnumRequest $request
     * @return JsonResponse
     */
    public function enums(HelpEnumRequest $request): JsonResponse
    {
        $result = $this->helpService->getEnums($request);

        return successResponse($result);
    }
}
