<?php

namespace App\Services\Setting;

use App\Models\SiteSetting;
use App\Services\Global\UploadService;
use Illuminate\Support\Facades\DB;

class SiteSettingService
{
    protected UploadService $uploadService;

    public function __construct(UploadService $uploadService)
    {
        $this->uploadService = $uploadService;
    }

    /**
     * Get all settings grouped by category
     */
    public function getAllSettings(): array
    {
        $settings = SiteSetting::all()->groupBy('group');

        return [
            'general' => $this->formatSettingsGroup($settings->get('general', collect())),
            'contact' => $this->formatSettingsGroup($settings->get('contact', collect())),
            'social' => $this->formatSettingsGroup($settings->get('social', collect())),
            'seo' => $this->formatSettingsGroup($settings->get('seo', collect())),
            'theme' => $this->formatSettingsGroup($settings->get('theme', collect())),
            'maintenance' => $this->formatSettingsGroup($settings->get('maintenance', collect()))
        ];
    }

    /**
     * Update multiple settings
     */
    public function updateSettings(array $data): array
    {
        return DB::transaction(function () use ($data) {
            foreach ($data as $key => $value) {
                $group = $this->getSettingGroup($key);
                $type = $this->getSettingType($key);

                // Handle file uploads
                if (in_array($key, ['site_logo', 'site_favicon']) && $value) {
                    // Delete old file if exists
                    $oldSetting = SiteSetting::where('key', $key)->first();
                    if ($oldSetting && $oldSetting->value) {
                        $this->uploadService->deleteFile($oldSetting->value);
                    }

                    $value = $this->uploadService->uploadFile($value, 'settings');
                }

                SiteSetting::set($key, $value, $type, $group);
            }

            return $this->getAllSettings();
        });
    }

    /**
     * Get settings by group
     */
    public function getSettingsByGroup(string $group): array
    {
        $settings = SiteSetting::where('group', $group)->get();
        return $this->formatSettingsGroup($settings);
    }

    /**
     * Get public settings for frontend (non-sensitive data)
     */
    public function getPublicSettings(): array
    {
        $publicKeys = [
            'site_name',
            'site_description',
            'site_logo',
            'site_favicon',
            'contact_phone',
            'contact_email',
            'contact_address',
            'contact_working_hours',
            'social_facebook',
            'social_twitter',
            'social_instagram',
            'social_linkedin',
            'social_youtube',
            'seo_meta_title',
            'seo_meta_description',
            'seo_meta_keywords',
            'theme_primary_color',
            'theme_secondary_color',
            'theme_font_family',
            'maintenance_mode',
            'maintenance_message'
        ];

        $settings = [];
        foreach ($publicKeys as $key) {
            $value = SiteSetting::get($key);
            if ($value !== null) {
                // Convert file paths to full URLs
                if (in_array($key, ['site_logo', 'site_favicon']) && $value) {
                    $value = asset('storage/' . $value);
                }
                $settings[$key] = $value;
            }
        }

        return $settings;
    }

    /**
     * Get single setting value
     */
    public function getSetting(string $key, $default = null)
    {
        return SiteSetting::get($key, $default);
    }

    /**
     * Set single setting value
     */
    public function setSetting(string $key, $value, string $type = 'text', string $group = 'general'): bool
    {
        try {
            SiteSetting::set($key, $value, $type, $group);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Format settings group for API response
     */
    private function formatSettingsGroup($settings): array
    {
        $formatted = [];

        foreach ($settings as $setting) {
            $value = $setting->value;

            // Convert file paths to full URLs for image types
            if ($setting->type === 'image' && $value) {
                $value = asset('storage/' . $value);
            }

            $formatted[$setting->key] = [
                'value' => $value,
                'type' => $setting->type,
                'updated_at' => $setting->updated_at?->toISOString()
            ];
        }

        return $formatted;
    }

    /**
     * Get setting group based on key
     */
    private function getSettingGroup(string $key): string
    {
        $groupMap = [
            'site_name' => 'general',
            'site_description' => 'general',
            'site_logo' => 'general',
            'site_favicon' => 'general',
            'homepage_id' => 'general',
            'contact_email' => 'contact',
            'contact_phone' => 'contact',
            'contact_address' => 'contact',
            'contact_working_hours' => 'contact',
            'social_facebook' => 'social',
            'social_twitter' => 'social',
            'social_instagram' => 'social',
            'social_linkedin' => 'social',
            'social_youtube' => 'social',
            'seo_meta_title' => 'seo',
            'seo_meta_description' => 'seo',
            'seo_meta_keywords' => 'seo',
            'seo_google_analytics' => 'seo',
            'seo_google_tag_manager' => 'seo',
            'theme_primary_color' => 'theme',
            'theme_secondary_color' => 'theme',
            'theme_font_family' => 'theme',
            'maintenance_mode' => 'maintenance',
            'maintenance_message' => 'maintenance'
        ];

        return $groupMap[$key] ?? 'general';
    }

    /**
     * Get setting type based on key
     */
    private function getSettingType(string $key): string
    {
        $typeMap = [
            'site_logo' => 'image',
            'site_favicon' => 'image',
            'site_description' => 'textarea',
            'contact_address' => 'textarea',
            'seo_meta_description' => 'textarea',
            'seo_google_analytics' => 'textarea',
            'seo_google_tag_manager' => 'textarea',
            'maintenance_message' => 'textarea',
            'maintenance_mode' => 'boolean',
            'homepage_id' => 'number'
        ];

        return $typeMap[$key] ?? 'text';
    }
}
