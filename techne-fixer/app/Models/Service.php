<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Service extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'subtitle',
        'description',
        'long_description',
        'icon',
        'status',
        'featured',
        'order',
        'image',
        'gallery_images',
    ];

    protected $casts = [
        'featured' => 'boolean',
        'gallery_images' => 'array',
    ];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($service) {
            if (empty($service->slug)) {
                $service->slug = Str::slug($service->name);
            }
        });
    }

    /**
     * Relationships
     */
    
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function serviceTypes()
    {
        return $this->belongsToMany(ServiceType::class, 'service_service_type')
            ->withTimestamps()
            ->orderBy('order');
    }

    public function scopeOfWorks()
    {
        return $this->hasMany(ScopeOfWork::class)
            ->where('status', 'active')
            ->orderBy('order');
    }

    /**
     * Scopes
     */
    
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeFeatured($query)
    {
        return $query->where('featured', true);
    }

    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%")
              ->orWhere('subtitle', 'like', "%{$search}%")
              ->orWhere('long_description', 'like', "%{$search}%");
        });
    }

    public function scopeByServiceType($query, $serviceTypeId)
    {
        return $query->whereHas('serviceTypes', function ($q) use ($serviceTypeId) {
            $q->where('service_types.id', $serviceTypeId);
        });
    }

    /**
     * Helper Methods
     */
    
    public function hasServiceType($serviceTypeId)
    {
        return $this->serviceTypes()->where('service_types.id', $serviceTypeId)->exists();
    }

    public function attachServiceTypes(array $serviceTypeIds)
    {
        $this->serviceTypes()->sync($serviceTypeIds);
    }

    public function addScopeOfWork(string $description, int $order = 0)
    {
        return $this->scopeOfWorks()->create([
            'description' => $description,
            'order' => $order,
            'status' => 'active',
        ]);
    }

    public function updateScopeOfWorks(array $works)
    {
        // Delete existing
        $this->scopeOfWorks()->delete();

        // Add new ones
        foreach ($works as $index => $work) {
            $this->addScopeOfWork($work, $index);
        }
    }
}