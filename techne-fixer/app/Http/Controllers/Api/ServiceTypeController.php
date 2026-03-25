<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ServiceType;

class ServiceTypeController extends Controller
{
    /**
     * GET /api/admin/service-types
     */
    public function index()
    {
        $types = ServiceType::active()
            ->ordered()
            ->get(['id', 'name', 'slug', 'color', 'icon']);

        return response()->json([
            'success' => true,
            'message' => 'Service types fetched successfully.',
            'data'    => $types,
        ]);
    }
}