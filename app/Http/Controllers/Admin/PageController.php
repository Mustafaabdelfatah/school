<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Str;

class PageController extends Controller
{
    public function index(): View
    {
        $pages = Page::with('parent')
            ->orderBy('parent_id')
            ->orderBy('sort_order')
            ->paginate(20);

        return view('admin.pages.index', compact('pages'));
    }

    public function create(): View
    {
        $parentPages = Page::whereNull('parent_id')->active()->get();
        $templates = $this->getAvailableTemplates();

        return view('admin.pages.create', compact('parentPages', 'templates'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:pages',
            'content' => 'nullable|string',
            'meta_description' => 'nullable|string|max:160',
            'meta_keywords' => 'nullable|string',
            'parent_id' => 'nullable|exists:pages,id',
            'sort_order' => 'integer|min:0',
            'is_active' => 'boolean',
            'show_in_menu' => 'boolean',
            'menu_icon' => 'nullable|string|max:100',
            'template' => 'nullable|string|max:100',
            'featured_image' => 'nullable|image|max:2048'
        ]);

        // Generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        // Handle featured image upload
        if ($request->hasFile('featured_image')) {
            $validated['featured_image'] = $request->file('featured_image')->store('pages', 'public');
        }

        Page::create($validated);

        return redirect()->route('admin.pages.index')
            ->with('success', 'تم إنشاء الصفحة بنجاح');
    }

    public function show(Page $page): View
    {
        $page->load('sections', 'children');
        return view('admin.pages.show', compact('page'));
    }

    public function edit(Page $page): View
    {
        $parentPages = Page::whereNull('parent_id')
            ->where('id', '!=', $page->id)
            ->active()
            ->get();
        $templates = $this->getAvailableTemplates();

        return view('admin.pages.edit', compact('page', 'parentPages', 'templates'));
    }

    public function update(Request $request, Page $page): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:pages,slug,' . $page->id,
            'content' => 'nullable|string',
            'meta_description' => 'nullable|string|max:160',
            'meta_keywords' => 'nullable|string',
            'parent_id' => 'nullable|exists:pages,id',
            'sort_order' => 'integer|min:0',
            'is_active' => 'boolean',
            'show_in_menu' => 'boolean',
            'menu_icon' => 'nullable|string|max:100',
            'template' => 'nullable|string|max:100',
            'featured_image' => 'nullable|image|max:2048'
        ]);

        // Generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        // Handle featured image upload
        if ($request->hasFile('featured_image')) {
            $validated['featured_image'] = $request->file('featured_image')->store('pages', 'public');
        }

        $page->update($validated);

        return redirect()->route('admin.pages.index')
            ->with('success', 'تم تحديث الصفحة بنجاح');
    }

    public function destroy(Page $page): RedirectResponse
    {
        $page->delete();

        return redirect()->route('admin.pages.index')
            ->with('success', 'تم حذف الصفحة بنجاح');
    }

    private function getAvailableTemplates(): array
    {
        return [
            'default' => 'افتراضي',
            'about' => 'عن الجامعة',
            'news' => 'الأخبار',
            'contact' => 'اتصل بنا',
            'programs' => 'البرامج الأكاديمية'
        ];
    }
}
