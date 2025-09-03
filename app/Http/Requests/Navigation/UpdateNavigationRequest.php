<?php

namespace App\Http\Requests\Navigation;

use Illuminate\Foundation\Http\FormRequest;

class UpdateNavigationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Add your authorization logic here
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $navigationId = $this->route('navigation')->id;

        return [
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255',
            'url' => 'nullable|string|max:255',
            'page_id' => 'nullable|exists:pages,id',
            'parent_id' => 'nullable|exists:navigations,id|not_in:' . $navigationId,
            'icon' => 'nullable|string|max:100',
            'target' => 'nullable|in:_self,_blank',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
            'location' => 'required|in:header,footer,sidebar'
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_active' => $this->boolean('is_active', true),
            'sort_order' => $this->integer('sort_order', 0),
            'target' => $this->input('target', '_self')
        ]);
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'اسم عنصر التنقل مطلوب',
            'name.max' => 'اسم عنصر التنقل يجب ألا يتجاوز 255 حرف',
            'page_id.exists' => 'الصفحة المحددة غير موجودة',
            'parent_id.exists' => 'عنصر التنقل الأب غير موجود',
            'parent_id.not_in' => 'لا يمكن أن يكون عنصر التنقل أب لنفسه',
            'location.required' => 'موقع عنصر التنقل مطلوب',
            'location.in' => 'موقع عنصر التنقل يجب أن يكون: header, footer, أو sidebar',
            'target.in' => 'هدف الرابط يجب أن يكون: _self أو _blank'
        ];
    }
}
