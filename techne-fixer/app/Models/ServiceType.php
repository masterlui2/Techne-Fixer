<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ServiceType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'color',
        'icon',
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

        static::creating(function ($serviceType) {
            if (empty($serviceType->slug)) {
                $serviceType->slug = Str::slug($serviceType->name);
            }
        });
    }

    /**
     * Relationships
     */
    
    public function services()
    {
        return $this->belongsToMany(Service::class, 'service_service_type')
            ->withTimestamps()
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
}