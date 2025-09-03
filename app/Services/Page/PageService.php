<?php

namespace App\Services\Page;

use App\Models\Page;
use App\Models\PageSection;
use App\Services\Global\UploadService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PageService
{
    protected UploadService $uploadService;

    public function __construct(UploadService $uploadService)
    {
        $this->uploadService = $uploadService;
    }

    /**
     * Get all pages with filters and pagination
     */
    public function getAllPages(array $filters = []): LengthAwarePaginator
    {
        $query = Page::with(['parent', 'children'])
            ->when(isset($filters['search']), function ($q) use ($filters) {
                $q->where(function ($query) use ($filters) {
                    $query->where('title', 'LIKE', "%{$filters['search']}%")
                          ->orWhere('content', 'LIKE', "%{$filters['search']}%");
                });
            })
            ->when(isset($filters['parent_id']), function ($q) use ($filters) {
                $q->where('parent_id', $filters['parent_id']);
            })
            ->when(isset($filters['is_active']), function ($q) use ($filters) {
                $q->where('is_active', $filters['is_active']);
            })
            ->when(isset($filters['template']), function ($q) use ($filters) {
                $q->where('template', $filters['template']);
            })
            ->orderBy('parent_id')
            ->orderBy('sort_order');

        $perPage = $filters['per_page'] ?? 15;
        return $query->paginate($perPage);
    }

    /**
     * Create a new page with sections
     */
    public function createPage(array $data): Page
    {
        return DB::transaction(function () use ($data) {
            // Handle featured image upload
            if (isset($data['featured_image'])) {
                $data['featured_image'] = $this->uploadService->uploadFile(
                    $data['featured_image'],
                    'pages'
                );
            }

            // Generate unique slug if not provided
            if (empty($data['slug'])) {
                $data['slug'] = $this->generateUniqueSlug($data['title']);
            }

            // Create the page
            $page = Page::create($data);

            // Create sections if provided
            if (isset($data['sections']) && is_array($data['sections'])) {
                $this->createPageSections($page, $data['sections']);
            }

            return $page->load('sections', 'parent', 'children');
        });
    }

    /**
     * Update an existing page
     */
    public function updatePage(Page $page, array $data): Page
    {
        return DB::transaction(function () use ($page, $data) {
            // Handle featured image upload
            if (isset($data['featured_image'])) {
                // Delete old image if exists
                if ($page->featured_image) {
                    $this->uploadService->deleteFile($page->featured_image);
                }

                $data['featured_image'] = $this->uploadService->uploadFile(
                    $data['featured_image'],
                    'pages'
                );
            }

            // Generate unique slug if changed
            if (isset($data['title']) && ($data['slug'] !== $page->slug || empty($data['slug']))) {
                $data['slug'] = $this->generateUniqueSlug($data['title'], $page->id);
            }

            // Update the page
            $page->update($data);

            // Update sections if provided
            if (isset($data['sections']) && is_array($data['sections'])) {
                $this->updatePageSections($page, $data['sections']);
            }

            return $page->load('sections', 'parent', 'children');
        });
    }

    /**
     * Delete a page
     */
    public function deletePage(Page $page): bool
    {
        return DB::transaction(function () use ($page) {
            // Delete featured image if exists
            if ($page->featured_image) {
                $this->uploadService->deleteFile($page->featured_image);
            }

            // Delete page sections and their images
            foreach ($page->sections as $section) {
                if ($section->image) {
                    $this->uploadService->deleteFile($section->image);
                }
            }

            return $page->delete();
        });
    }

    /**
     * Get page with all relations loaded
     */
    public function getPageWithRelations(Page $page): Page
    {
        return $page->load(['sections', 'parent', 'children']);
    }

    /**
     * Get page by slug with nested navigation
     */
    public function getPageBySlug(string $slug): ?Page
    {
        // Handle nested slugs (e.g., about-university/administration)
        $slugParts = explode('/', $slug);
        $currentPage = null;
        $parentId = null;

        foreach ($slugParts as $slugPart) {
            $currentPage = Page::where('slug', $slugPart)
                ->where('parent_id', $parentId)
                ->active()
                ->first();

            if (!$currentPage) {
                return null;
            }

            $parentId = $currentPage->id;
        }

        return $currentPage->load(['sections', 'parent', 'children']);
    }

    /**
     * Get navigation structure for frontend
     */
    public function getNavigationStructure(): array
    {
        $pages = Page::active()
            ->whereNull('parent_id')
            ->where('show_in_menu', true)
            ->with(['children' => function ($query) {
                $query->active()->where('show_in_menu', true)->orderBy('sort_order');
            }])
            ->orderBy('sort_order')
            ->get();

        return $this->buildNavigationTree($pages);
    }

    /**
     * Create page sections
     */
    private function createPageSections(Page $page, array $sections): void
    {
        foreach ($sections as $sectionData) {
            // Handle section image upload
            if (isset($sectionData['image'])) {
                $sectionData['image'] = $this->uploadService->uploadFile(
                    $sectionData['image'],
                    'page-sections'
                );
            }

            $sectionData['page_id'] = $page->id;
            PageSection::create($sectionData);
        }
    }

    /**
     * Update page sections
     */
    private function updatePageSections(Page $page, array $sections): void
    {
        // Delete existing sections
        foreach ($page->sections as $section) {
            if ($section->image) {
                $this->uploadService->deleteFile($section->image);
            }
        }
        $page->sections()->delete();

        // Create new sections
        $this->createPageSections($page, $sections);
    }

    /**
     * Generate unique slug
     */
    private function generateUniqueSlug(string $title, int $excludeId = null): string
    {
        $slug = Str::slug($title);
        $originalSlug = $slug;
        $counter = 1;

        while (Page::where('slug', $slug)
            ->when($excludeId, function ($query) use ($excludeId) {
                $query->where('id', '!=', $excludeId);
            })
            ->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    /**
     * Build navigation tree structure
     */
    private function buildNavigationTree(Collection $pages): array
    {
        return $pages->map(function ($page) {
            return [
                'id' => $page->id,
                'title' => $page->title,
                'slug' => $page->slug,
                'full_slug' => $page->full_slug,
                'icon' => $page->menu_icon,
                'children' => $this->buildNavigationTree($page->children)
            ];
        })->toArray();
    }
}
