<?php

namespace App\Http\Requests\Global\Setting;

use Illuminate\Foundation\Http\FormRequest;

class SettingRequest extends FormRequest
{

    public function rules(): array
    {
        return [
            'settings' => ['required', 'array'],
            'settings.*.key' => ['required', 'string'],
            'settings.*.value' => ['nullable'],
            'settings.*.group' => ['required', 'string'],
            'settings.*.model' => ['nullable', 'string'],
        ];
    }
}
