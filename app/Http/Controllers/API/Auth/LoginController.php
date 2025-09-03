<?php

namespace App\Http\Controllers\API\Auth;

use App\Exceptions\InactiveUserException;
use App\Exceptions\InvalidEmailAndPasswordCombinationException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\Auth\LoginResource;
use App\Models\User;
use App\Services\Auth\LoginService;
use Illuminate\Http\JsonResponse;

class LoginController extends Controller
{
    /**
     * @var LoginService $loginService
     */
    protected LoginService $loginService;

    /**
     * @param LoginService $loginService
     */
    public function __construct(LoginService $loginService)
    {
        $this->loginService = $loginService;
    }

    /**
     * @param LoginRequest $request
     * @return JsonResponse
     * @throws InvalidEmailAndPasswordCombinationException
     */
    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $user = $this->loginService
                ->setGuard("api")
                ->setModel(User::class)
                ->attempt($request);

            return successResponse(new LoginResource($user['user'], $user['token']), __('api.login_success'));
        } catch (InvalidEmailAndPasswordCombinationException $e) {
            return unauthorizedResponse(__('api.invalid_email_and_password'));
        } catch (InactiveUserException $e) {
            return forbiddenResponse(__('api.account_not_active'));
        }
    }

    /**
     * @return JsonResponse
     */
    public function logout(): JsonResponse
    {
        auth()->user()
            ->tokens()
            ->where('id', auth()->user()->currentAccessToken()->id)
            ->delete();

        return successResponse(null, __('api.user_logged_out'));
    }
}
