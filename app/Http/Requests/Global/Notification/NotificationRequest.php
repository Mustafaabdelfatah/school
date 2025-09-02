<?php

namespace App\Http\Requests\Global\Notification;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class NotificationRequest extends FormRequest
{

    public function rules(): array
    {
        return [
            'action' => 'required|in:open,read',
            'ids' => 'required_if:action,read|array',
            'ids.*' => ['required_with:ids', Rule::exists('notifications', 'id')],
        ];
    }
}
