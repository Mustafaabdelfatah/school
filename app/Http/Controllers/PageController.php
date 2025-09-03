<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Models\Navigation;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\Response;

class PageController extends Controller
{
    public function show(Request $request, $slug = null): View
    {
        // Handle homepage
        if (!$slug) {
            $page = Page::where('slug', 'home')
                ->orWhere('slug', 'homepage')
                ->orWhere('id', SiteSetting::get('homepage_id'))
                ->active()
                ->first();

            if (!$page) {
                $page = Page::active()->orderBy('sort_order')->first();
            }
        } else {
            // Handle nested pages (e.g., about-university/administration)
            $slugParts = explode('/', $slug);
            $page = $this->findPageBySlugPath($slugParts);
        }

        if (!$page) {
            abort(404);
        }

        // Load page sections
        $page->load('sections');

        // Get navigation for header
        $headerNavigation = Navigation::active()
            ->location('header')
            ->mainMenu()
            ->with('children')
            ->get();

        // Get breadcrumbs
        $breadcrumbs = $page->breadcrumbs;

        // Get site settings
        $siteSettings = $this->getSiteSettings();

        return view('frontend.page', compact('page', 'headerNavigation', 'breadcrumbs', 'siteSettings'));
    }

    private function findPageBySlugPath(array $slugParts): ?Page
    {
        $currentPage = null;
        $parentId = null;

        foreach ($slugParts as $slug) {
            $currentPage = Page::where('slug', $slug)
                ->where('parent_id', $parentId)
                ->active()
                ->first();

            if (!$currentPage) {
                return null;
            }

            $parentId = $currentPage->id;
        }

        return $currentPage;
    }

    private function getSiteSettings(): array
    {
        return [
            'site_name' => SiteSetting::get('site_name', 'جامعة دمشق'),
            'site_logo' => SiteSetting::get('site_logo'),
            'contact_phone' => SiteSetting::get('contact_phone'),
            'contact_email' => SiteSetting::get('contact_email'),
            'social_facebook' => SiteSetting::get('social_facebook'),
            'social_twitter' => SiteSetting::get('social_twitter'),
            'social_instagram' => SiteSetting::get('social_instagram'),
        ];
    }

    public function search(Request $request): View
    {
        $query = $request->get('q');
        $pages = Page::active()
            ->where(function ($q) use ($query) {
                $q->where('title', 'LIKE', "%{$query}%")
                  ->orWhere('content', 'LIKE', "%{$query}%");
            })
            ->paginate(10);

        $headerNavigation = Navigation::active()
            ->location('header')
            ->mainMenu()
            ->with('children')
            ->get();

        $siteSettings = $this->getSiteSettings();

        return view('frontend.search', compact('pages', 'query', 'headerNavigation', 'siteSettings'));
    }
}
