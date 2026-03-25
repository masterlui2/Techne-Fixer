<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScopeOfWork extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_id',
        'service_type_id',
        'description',
        'order',
        'status',
    ];

    protected $casts = [
        'status' => 'string',
    ];

    /**
     * Relationships
     */
    
    public function serviceType()
    {
        return $this->belongsTo(ServiceType::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
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

    public function scopeForService($query, $serviceId)
    {
        return $query->where('service_id', $serviceId);
    }
    
}