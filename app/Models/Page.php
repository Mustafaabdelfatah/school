<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Page extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'content',
        'meta_description',
        'meta_keywords',
        'parent_id',
        'sort_order',
        'is_active',
        'show_in_menu',
        'menu_icon',
        'template',
        'featured_image'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'show_in_menu' => 'boolean',
        'sort_order' => 'integer'
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Page::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Page::class, 'parent_id')->where('is_active', true)->orderBy('sort_order');
    }

    public function allChildren(): HasMany
    {
        return $this->hasMany(Page::class, 'parent_id')->orderBy('sort_order');
    }

    public function sections(): HasMany
    {
        return $this->hasMany(PageSection::class)->orderBy('sort_order');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeMainMenu($query)
    {
        return $query->where('show_in_menu', true)->whereNull('parent_id')->orderBy('sort_order');
    }

    public function getFullSlugAttribute(): string
    {
        if ($this->parent) {
            return $this->parent->full_slug . '/' . $this->slug;
        }
        return $this->slug;
    }

    public function getBreadcrumbsAttribute(): array
    {
        $breadcrumbs = [];
        $page = $this;

        while ($page) {
            array_unshift($breadcrumbs, [
                'title' => $page->title,
                'url' => $page->full_slug,
                'id' => $page->id
            ]);
            $page = $page->parent;
        }

        return $breadcrumbs;
    }
}
