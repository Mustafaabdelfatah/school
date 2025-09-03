<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Navigation extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'url',
        'type',
        'page_id',
        'parent_id',
        'sort_order',
        'is_active',
        'target',
        'icon',
        'css_class'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'page_id' => 'integer',
        'parent_id' => 'integer'
    ];

    /**
     * Get the page associated with this navigation item
     */
    public function page()
    {
        return $this->belongsTo(Page::class);
    }

    /**
     * Get the parent navigation item
     */
    public function parent()
    {
        return $this->belongsTo(Navigation::class, 'parent_id');
    }

    /**
     * Get the child navigation items
     */
    public function children()
    {
        return $this->hasMany(Navigation::class, 'parent_id')->where('is_active', true)->orderBy('sort_order');
    }

    /**
     * Get all child navigation items (including inactive)
     */
    public function allChildren()
    {
        return $this->hasMany(Navigation::class, 'parent_id')->orderBy('sort_order');
    }

    /**
     * Scope to get active navigation items
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to order navigation items
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }

    /**
     * Scope to get top-level navigation items
     */
    public function scopeTopLevel($query)
    {
        return $query->whereNull('parent_id');
    }
}
