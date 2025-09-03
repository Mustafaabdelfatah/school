<?php

namespace App\Http\Requests\Setting;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSiteSettingRequest extends FormRequest
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
        return [
            // General Settings
            'site_name' => 'nullable|string|max:255',
            'site_description' => 'nullable|string|max:500',
            'site_logo' => 'nullable|image|max:2048',
            'site_favicon' => 'nullable|image|max:512',
            'homepage_id' => 'nullable|exists:pages,id',

            // Contact Settings
            'contact_email' => 'nullable|email|max:255',
            'contact_phone' => 'nullable|string|max:20',
            'contact_address' => 'nullable|string|max:500',
            'contact_working_hours' => 'nullable|string|max:255',

            // Social Media Settings
            'social_facebook' => 'nullable|url|max:255',
            'social_twitter' => 'nullable|url|max:255',
            'social_instagram' => 'nullable|url|max:255',
            'social_linkedin' => 'nullable|url|max:255',
            'social_youtube' => 'nullable|url|max:255',

            // SEO Settings
            'seo_meta_title' => 'nullable|string|max:60',
            'seo_meta_description' => 'nullable|string|max:160',
            'seo_meta_keywords' => 'nullable|string|max:255',
            'seo_google_analytics' => 'nullable|string',
            'seo_google_tag_manager' => 'nullable|string',

            // Theme Settings
            'theme_primary_color' => 'nullable|string|max:7',
            'theme_secondary_color' => 'nullable|string|max:7',
            'theme_font_family' => 'nullable|string|max:100',

            // Maintenance Mode
            'maintenance_mode' => 'nullable|boolean',
            'maintenance_message' => 'nullable|string|max:500'
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'site_name.max' => 'اسم الموقع يجب ألا يتجاوز 255 حرف',
            'site_description.max' => 'وصف الموقع يجب ألا يتجاوز 500 حرف',
            'site_logo.image' => 'شعار الموقع يجب أن يكون صورة صالحة',
            'site_logo.max' => 'حجم شعار الموقع يجب ألا يتجاوز 2 ميجابايت',
            'site_favicon.image' => 'أيقونة الموقع يجب أن تكون صورة صالحة',
            'site_favicon.max' => 'حجم أيقونة الموقع يجب ألا يتجاوز 512 كيلوبايت',
            'homepage_id.exists' => 'الصفحة الرئيسية المحددة غير موجودة',
            'contact_email.email' => 'البريد الإلكتروني غير صالح',
            'social_facebook.url' => 'رابط فيسبوك غير صالح',
            'social_twitter.url' => 'رابط تويتر غير صالح',
            'social_instagram.url' => 'رابط إنستغرام غير صالح',
            'social_linkedin.url' => 'رابط لينكد إن غير صالح',
            'social_youtube.url' => 'رابط يوتيوب غير صالح',
            'seo_meta_title.max' => 'عنوان SEO يجب ألا يتجاوز 60 حرف',
            'seo_meta_description.max' => 'وصف SEO يجب ألا يتجاوز 160 حرف'
        ];
    }
}
