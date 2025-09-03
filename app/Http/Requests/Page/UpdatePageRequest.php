<?php

namespace App\Http\Requests\Page;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class UpdatePageRequest extends FormRequest
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
        $pageId = $this->route('page')->id;

        return [
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:pages,slug,' . $pageId,
            'content' => 'nullable|string',
            'meta_description' => 'nullable|string|max:160',
            'meta_keywords' => 'nullable|string|max:255',
            'parent_id' => 'nullable|exists:pages,id',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
            'show_in_menu' => 'nullable|boolean',
            'menu_icon' => 'nullable|string|max:100',
            'template' => 'nullable|string|max:100',
            'featured_image' => 'nullable|image|max:2048',
            'sections' => 'nullable|array',
            'sections.*.type' => 'required_with:sections|string|in:text,image,video,hero,cards,gallery',
            'sections.*.title' => 'nullable|string|max:255',
            'sections.*.content' => 'nullable|string',
            'sections.*.image' => 'nullable|image|max:2048',
            'sections.*.video_url' => 'nullable|url',
            'sections.*.button_text' => 'nullable|string|max:100',
            'sections.*.button_url' => 'nullable|string|max:255',
            'sections.*.sort_order' => 'nullable|integer|min:0',
            'sections.*.is_active' => 'nullable|boolean',
            'sections.*.settings' => 'nullable|array'
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        if (empty($this->slug) && !empty($this->title)) {
            $this->merge([
                'slug' => Str::slug($this->title)
            ]);
        }

        $this->merge([
            'is_active' => $this->boolean('is_active', true),
            'show_in_menu' => $this->boolean('show_in_menu', true),
            'sort_order' => $this->integer('sort_order', 0)
        ]);
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'title.required' => 'عنوان الصفحة مطلوب',
            'title.max' => 'عنوان الصفحة يجب ألا يتجاوز 255 حرف',
            'slug.unique' => 'رابط الصفحة مستخدم بالفعل',
            'meta_description.max' => 'وصف الصفحة يجب ألا يتجاوز 160 حرف',
            'parent_id.exists' => 'الصفحة الأب غير موجودة',
            'featured_image.image' => 'الصورة المميزة يجب أن تكون صورة صالحة',
            'featured_image.max' => 'حجم الصورة المميزة يجب ألا يتجاوز 2 ميجابايت'
        ];
    }
}
