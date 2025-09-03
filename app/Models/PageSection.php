<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PageSection extends Model
{
    use HasFactory;

    protected $fillable = [
        'page_id',
        'type',
        'title',
        'content',
        'image',
        'data',
        'order',
        'is_active'
    ];

    protected $casts = [
        'data' => 'json',
        'is_active' => 'boolean',
        'order' => 'integer'
    ];

    /**
     * Get the page that owns the section
     */
    public function page()
    {
        return $this->belongsTo(Page::class);
    }

    /**
     * Scope to get active sections
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to order sections
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }
}
