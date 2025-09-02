<?php

namespace App\Http\Requests\Global\Setting;

use Illuminate\Foundation\Http\FormRequest;

class TestCredentialsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return string[]
     */
    public function rules(): array
    {
        return [
            'email' => 'required|email',
            'body' => 'required|string',
        ];
    }
}
