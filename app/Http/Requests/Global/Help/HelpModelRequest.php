<?php

namespace App\Http\Requests\Global\Help;

use Illuminate\Foundation\Http\FormRequest;

class HelpModelRequest extends FormRequest
{

    public function rules(): array
    {
        return [
            'tables' => ['required', 'array'],
            'tables.*.name' => ['required', 'string'],
            'tables.*.columns' => ['sometimes', 'required', 'array'],
            'tables.*.columns.*' => ['sometimes', 'required', 'string'],
        ];
    }
}
