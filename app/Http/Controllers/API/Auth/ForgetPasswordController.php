<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ForgetPasswordRequest;
use App\Http\Requests\Auth\VerifyOTPRequest;
use App\Models\User;
use App\Services\Auth\ForgetPasswordService;
use Illuminate\Http\JsonResponse;
use Random\RandomException;

class ForgetPasswordController extends Controller
{
    protected ForgetPasswordService $forgetPasswordService;

    /**
     * @param ForgetPasswordService $forgetPasswordService
     */
    public function __construct(ForgetPasswordService $forgetPasswordService)
    {
        $this->forgetPasswordService = $forgetPasswordService;
    }

    /**
     * @param ForgetPasswordRequest $request
     * @return JsonResponse
     * @throws RandomException
     */
    public function forget(ForgetPasswordRequest $request): JsonResponse
    {
        $otp = $this->forgetPasswordService->request($request);

        if (!$otp) {
            return failResponse(__('api.email_not_registered'));
        }

        return successResponse(['otp' => true], __('api.reset_password_send_success'));
    }

    /**
     * @param VerifyOTPRequest $request
     * @return JsonResponse
     */
    public function verify(VerifyOTPRequest $request): JsonResponse
    {
        $user = User::where(['email' => $request->only('email')])->first();

        if ($user->otp != $request->otp) {
            return failResponse(msg: __('passwords.invalid_otp'));
        }

        return successResponse(msg: __('passwords.otp_verified'));
    }
}
