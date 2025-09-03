<?php

namespace App\Http\Resources\Navigation;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NavigationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'url' => $this->url,
            'icon' => $this->icon,
            'target' => $this->target,
            'sort_order' => $this->sort_order,
            'is_active' => $this->is_active,
            'location' => $this->location,
            'page' => $this->whenLoaded('page', function () {
                return [
                    'id' => $this->page->id,
                    'title' => $this->page->title,
                    'slug' => $this->page->slug,
                    'full_slug' => $this->page->full_slug
                ];
            }),
            'parent' => $this->whenLoaded('parent', function () {
                return new NavigationResource($this->parent);
            }),
            'children' => NavigationResource::collection($this->whenLoaded('children')),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString()
        ];
    }
}
