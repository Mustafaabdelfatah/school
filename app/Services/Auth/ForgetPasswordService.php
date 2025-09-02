<?php

namespace App\Services\Auth;

use App\Http\Requests\Auth\ForgetPasswordRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Models\User;
use App\Services\Global\NotificationService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\App;
use Random\RandomException;

class ForgetPasswordService
{
    protected User $user;

    /**
     * @param ForgetPasswordRequest $request
     * @return int
     * @throws RandomException
     */
    public function request(ForgetPasswordRequest $request): int
    {
        if (!$user = User::where(['email' => $request->only('email')])->first()) {
            return 0;
        }

        $otp = App::environment() != "production" ? 1111 : random_int(1000, 9999);

        $user->update([
            'otp' => $otp,
            'otp_expire_at' => Carbon::now()->addMinutes(10),
        ]);

        $user->sendNotification([
            'title' => 'reset_pw_title',
            'msg' => "reset_pw_otp|otp=$otp|name=$user->name",
        ], ['email']);

        return $otp;
    }

    /**
     * @param ResetPasswordRequest $request
     * @return boolean
     */
    public function reset(ResetPasswordRequest $request): bool
    {
        if (!$user = User::where(['email' => $request->email, 'otp' => $request->otp])->first()) {
            return false;
        }

        $user->update(['password' => $request->password, 'otp' => null]);

        return true;
    }
}
