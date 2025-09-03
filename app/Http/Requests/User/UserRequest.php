<?php

namespace App\Http\Requests\User;

use App\Models\Country;
use App\Rules\ValidLength;
use App\Rules\StrongPassword;
use App\Enum\User\UserTypeEnum;

use Illuminate\Validation\Rule;
use App\Enum\User\UserGenderEnum;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:50'],


            'phone' => [
                'nullable',
                'regex:/^[0-9]+$/',
            ],
            'email' => [
                'required', 'email',
                Rule::unique('users', 'email')->ignore($this->route('user'))
                    ->whereNull('deleted_at')
            ],
            'password' => ['sometimes', 'required', 'confirmed'],
            'type' => ['sometimes', 'required', new Enum(UserTypeEnum::class)],
            'avatar' => 'sometimes|nullable|' . vImage(),
            'roles' => ['required', Rule::exists('roles', 'id')],
             'permissions' => 'sometimes|nullable|array',
            'permissions.*' => 'required_with:permissions|exists:permissions,name',

        ];
    }
}
