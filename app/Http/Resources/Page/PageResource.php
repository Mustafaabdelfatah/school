<?php

namespace App\Http\Resources\Page;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PageResource extends JsonResource
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
            'title' => $this->title,
            'slug' => $this->slug,
            'full_slug' => $this->full_slug,
            'content' => $this->content,
            'meta_description' => $this->meta_description,
            'meta_keywords' => $this->meta_keywords,
            'sort_order' => $this->sort_order,
            'is_active' => $this->is_active,
            'show_in_menu' => $this->show_in_menu,
            'menu_icon' => $this->menu_icon,
            'template' => $this->template,
            'featured_image' => $this->featured_image ? asset('storage/' . $this->featured_image) : null,
            'breadcrumbs' => $this->breadcrumbs,
            'parent' => $this->whenLoaded('parent', function () {
                return new PageResource($this->parent);
            }),
            'children' => PageResource::collection($this->whenLoaded('children')),
            'sections' => PageSectionResource::collection($this->whenLoaded('sections')),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString()
        ];
    }
}
