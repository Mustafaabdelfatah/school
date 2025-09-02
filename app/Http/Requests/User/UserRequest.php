<?php

namespace App\Http\Requests\User;

use App\Models\Country;
use App\Rules\ValidLength;
use App\Rules\StrongPassword;
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
            'country_id' => ['required', Rule::exists('countries', 'id')],
            'nationality_id' => ['required', Rule::exists('countries', 'id')],
            'phone_code' => ['required', Rule::exists('countries', 'phone_code')],

            'phone' => [
                'nullable',
                'regex:/^[0-9]+$/',
                new ValidLength($this->input('country_id'), Country::class, 'phone_length'),
                Rule::unique('users', 'phone')->whereNull('deleted_at')->ignore($this->user?->id)
            ],
            'email' => [
                'required', 'email',
                Rule::unique('users', 'email')->ignore($this->route('user'))
                    ->whereNull('deleted_at')
            ],
            'password' => ['sometimes', 'required', 'confirmed', new StrongPassword($this->first_name, $this->middle_name, $this->last_name)],
            'gender' => ['required', new Enum(UserGenderEnum::class)],
            'avatar' => 'sometimes|nullable|' . vImage(),
            'roles' => ['required', Rule::exists('roles', 'id')],
            'is_active' => 'sometimes|boolean',
            'permissions' => 'sometimes|nullable|array',
            'permissions.*' => 'required_with:permissions|exists:permissions,name',
            'locations' => 'sometimes|nullable|array',
            'locations.*' => 'required_with:locations|exists:locations,id',
        ];
    }
}