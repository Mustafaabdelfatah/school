<?php

namespace Database\Seeders;

use App\Models\SiteSetting;
use Illuminate\Database\Seeder;

class SiteSettingSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // General Settings
        SiteSetting::create([
            'key' => 'site_name',
            'value' => 'جامعة دمشق',
            'type' => 'text',
            'group' => 'general'
        ]);

        SiteSetting::create([
            'key' => 'site_description',
            'value' => 'المؤسسة التعليمية الرئيسية للتعليم العالي في سوريا منذ عام 1923',
            'type' => 'textarea',
            'group' => 'general'
        ]);

        SiteSetting::create([
            'key' => 'site_logo',
            'value' => null,
            'type' => 'image',
            'group' => 'general'
        ]);

        SiteSetting::create([
            'key' => 'site_favicon',
            'value' => null,
            'type' => 'image',
            'group' => 'general'
        ]);

        // Contact Settings
        SiteSetting::create([
            'key' => 'contact_email',
            'value' => 'info@damascus-uni.edu.sy',
            'type' => 'text',
            'group' => 'contact'
        ]);

        SiteSetting::create([
            'key' => 'contact_phone',
            'value' => '+963-11-2139000',
            'type' => 'text',
            'group' => 'contact'
        ]);

        SiteSetting::create([
            'key' => 'contact_address',
            'value' => 'دمشق، الجمهورية العربية السورية',
            'type' => 'textarea',
            'group' => 'contact'
        ]);

        SiteSetting::create([
            'key' => 'contact_working_hours',
            'value' => 'السبت - الخميس: 8:00 ص - 4:00 م',
            'type' => 'text',
            'group' => 'contact'
        ]);

        // Social Media Settings
        SiteSetting::create([
            'key' => 'social_facebook',
            'value' => 'https://facebook.com/damascus.university',
            'type' => 'text',
            'group' => 'social'
        ]);

        SiteSetting::create([
            'key' => 'social_twitter',
            'value' => 'https://twitter.com/damascus_uni',
            'type' => 'text',
            'group' => 'social'
        ]);

        SiteSetting::create([
            'key' => 'social_instagram',
            'value' => 'https://instagram.com/damascus_university',
            'type' => 'text',
            'group' => 'social'
        ]);

        SiteSetting::create([
            'key' => 'social_linkedin',
            'value' => 'https://linkedin.com/company/damascus-university',
            'type' => 'text',
            'group' => 'social'
        ]);

        SiteSetting::create([
            'key' => 'social_youtube',
            'value' => 'https://youtube.com/damascusuniversity',
            'type' => 'text',
            'group' => 'social'
        ]);

        // SEO Settings
        SiteSetting::create([
            'key' => 'seo_meta_title',
            'value' => 'جامعة دمشق - التعليم العالي في سوريا',
            'type' => 'text',
            'group' => 'seo'
        ]);

        SiteSetting::create([
            'key' => 'seo_meta_description',
            'value' => 'جامعة دمشق هي المؤسسة التعليمية الرائدة في سوريا منذ عام 1923، تقدم برامج أكاديمية متنوعة وتميز في البحث العلمي',
            'type' => 'textarea',
            'group' => 'seo'
        ]);

        SiteSetting::create([
            'key' => 'seo_meta_keywords',
            'value' => 'جامعة دمشق، التعليم العالي، سوريا، الجامعات، البحث العلمي، التعليم الجامعي',
            'type' => 'text',
            'group' => 'seo'
        ]);

        SiteSetting::create([
            'key' => 'seo_google_analytics',
            'value' => null,
            'type' => 'textarea',
            'group' => 'seo'
        ]);

        SiteSetting::create([
            'key' => 'seo_google_tag_manager',
            'value' => null,
            'type' => 'textarea',
            'group' => 'seo'
        ]);

        // Theme Settings
        SiteSetting::create([
            'key' => 'theme_primary_color',
            'value' => '#2c3e50',
            'type' => 'text',
            'group' => 'theme'
        ]);

        SiteSetting::create([
            'key' => 'theme_secondary_color',
            'value' => '#3498db',
            'type' => 'text',
            'group' => 'theme'
        ]);

        SiteSetting::create([
            'key' => 'theme_font_family',
            'value' => 'Cairo, Arial, sans-serif',
            'type' => 'text',
            'group' => 'theme'
        ]);

        // Maintenance Settings
        SiteSetting::create([
            'key' => 'maintenance_mode',
            'value' => false,
            'type' => 'boolean',
            'group' => 'maintenance'
        ]);

        SiteSetting::create([
            'key' => 'maintenance_message',
            'value' => 'الموقع تحت الصيانة، يرجى المحاولة لاحقاً',
            'type' => 'textarea',
            'group' => 'maintenance'
        ]);
    }
}
