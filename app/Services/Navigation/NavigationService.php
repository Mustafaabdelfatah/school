<?php

namespace App\Services\Navigation;

use App\Models\Navigation;
use App\Models\Page;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class NavigationService
{
    /**
     * Get all navigation items with filters and pagination
     */
    public function getAllNavigations(array $filters = []): LengthAwarePaginator
    {
        $query = Navigation::with(['parent', 'children', 'page'])
            ->when(isset($filters['search']), function ($q) use ($filters) {
                $q->where('name', 'LIKE', "%{$filters['search']}%");
            })
            ->when(isset($filters['location']), function ($q) use ($filters) {
                $q->where('location', $filters['location']);
            })
            ->when(isset($filters['parent_id']), function ($q) use ($filters) {
                if ($filters['parent_id'] === 'null') {
                    $q->whereNull('parent_id');
                } else {
                    $q->where('parent_id', $filters['parent_id']);
                }
            })
            ->when(isset($filters['is_active']), function ($q) use ($filters) {
                $q->where('is_active', $filters['is_active']);
            })
            ->orderBy('location')
            ->orderBy('parent_id')
            ->orderBy('sort_order');

        $perPage = $filters['per_page'] ?? 15;
        return $query->paginate($perPage);
    }

    /**
     * Create a new navigation item
     */
    public function createNavigation(array $data): Navigation
    {
        return DB::transaction(function () use ($data) {
            // Set URL from page if page_id is provided
            if (isset($data['page_id']) && $data['page_id']) {
                $page = Page::find($data['page_id']);
                if ($page) {
                    $data['url'] = $page->full_slug;
                }
            }

            return Navigation::create($data);
        });
    }

    /**
     * Update an existing navigation item
     */
    public function updateNavigation(Navigation $navigation, array $data): Navigation
    {
        return DB::transaction(function () use ($navigation, $data) {
            // Set URL from page if page_id is provided
            if (isset($data['page_id']) && $data['page_id']) {
                $page = Page::find($data['page_id']);
                if ($page) {
                    $data['url'] = $page->full_slug;
                }
            } elseif (isset($data['page_id']) && !$data['page_id']) {
                // Clear URL if page_id is removed
                $data['url'] = $data['url'] ?? null;
            }

            $navigation->update($data);
            return $navigation->load(['parent', 'children', 'page']);
        });
    }

    /**
     * Delete a navigation item
     */
    public function deleteNavigation(Navigation $navigation): bool
    {
        return $navigation->delete();
    }

    /**
     * Get navigation with all relations loaded
     */
    public function getNavigationWithRelations(Navigation $navigation): Navigation
    {
        return $navigation->load(['parent', 'children', 'page']);
    }

    /**
     * Get navigation items by location
     */
    public function getNavigationByLocation(string $location): Collection
    {
        return Navigation::active()
            ->location($location)
            ->mainMenu()
            ->with(['children' => function ($query) {
                $query->active()->orderBy('sort_order');
            }, 'page'])
            ->get();
    }

    /**
     * Get header navigation structure for frontend
     */
    public function getHeaderNavigation(): array
    {
        $navigation = $this->getNavigationByLocation('header');
        return $this->buildNavigationTree($navigation);
    }

    /**
     * Get footer navigation structure for frontend
     */
    public function getFooterNavigation(): array
    {
        $navigation = $this->getNavigationByLocation('footer');
        return $this->buildNavigationTree($navigation);
    }

    /**
     * Get sidebar navigation structure for frontend
     */
    public function getSidebarNavigation(): array
    {
        $navigation = $this->getNavigationByLocation('sidebar');
        return $this->buildNavigationTree($navigation);
    }

    /**
     * Build navigation tree structure
     */
    private function buildNavigationTree(Collection $navigations): array
    {
        return $navigations->map(function ($nav) {
            return [
                'id' => $nav->id,
                'name' => $nav->name,
                'url' => $nav->url,
                'icon' => $nav->icon,
                'target' => $nav->target,
                'page' => $nav->page ? [
                    'id' => $nav->page->id,
                    'title' => $nav->page->title,
                    'slug' => $nav->page->slug
                ] : null,
                'children' => $this->buildNavigationTree($nav->children)
            ];
        })->toArray();
    }

    /**
     * Reorder navigation items
     */
    public function reorderNavigations(array $orders): bool
    {
        return DB::transaction(function () use ($orders) {
            foreach ($orders as $order) {
                Navigation::where('id', $order['id'])
                    ->update(['sort_order' => $order['sort_order']]);
            }
            return true;
        });
    }
}
