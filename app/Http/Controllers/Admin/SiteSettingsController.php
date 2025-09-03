<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SiteSettingsController extends Controller
{
    public function index(): View
    {
        $settings = [
            'general' => SiteSetting::getGroup('general'),
            'contact' => SiteSetting::getGroup('contact'),
            'social' => SiteSetting::getGroup('social'),
            'seo' => SiteSetting::getGroup('seo')
        ];

        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request): RedirectResponse
    {
        $settings = $request->except('_token', '_method');

        foreach ($settings as $key => $value) {
            SiteSetting::set($key, $value);
        }

        return redirect()->route('admin.settings.index')
            ->with('success', 'تم حفظ الإعدادات بنجاح');
    }

    public function uploadLogo(Request $request): RedirectResponse
    {
        $request->validate([
            'logo' => 'required|image|max:2048'
        ]);

        $logoPath = $request->file('logo')->store('settings', 'public');
        SiteSetting::set('site_logo', $logoPath, 'image', 'general');

        return redirect()->route('admin.settings.index')
            ->with('success', 'تم رفع الشعار بنجاح');
    }
}
