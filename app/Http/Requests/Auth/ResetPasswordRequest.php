<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class ResetPasswordRequest extends FormRequest
{

    public function rules(): array
    {
        return [
            'otp' => 'required|string|digits:4', 
            'email'     => 'required|email',
            'password'  => 'required|confirmed'
        ];
    }
}
