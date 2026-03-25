<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
        'image',
        'status',
        'order',
    ];

    protected $casts = [
        'status' => 'string',
    ];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });
    }

    /**
     * Relationships
     */
    
    public function services()
    {
        return $this->hasMany(Service::class)
            ->orderBy('order');
    }

    /**
     * Scopes
     */
    
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }

    public function scopeWithActiveServices($query)
    {
        return $query->whereHas('services', function ($q) {
            $q->where('status', 'active');
        });
    }

    /**
     * Accessors
     */
    
    public function getServicesCountAttribute()
    {
        return $this->services()->count();
    }

    public function getActiveServicesCountAttribute()
    {
        return $this->services()->where('status', 'active')->count();
    }
}