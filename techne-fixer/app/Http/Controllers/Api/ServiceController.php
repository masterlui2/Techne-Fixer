<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ServiceController extends Controller
{
    /**
     * Display a listing of services with optional filters.
     * GET /api/services?status=active&featured=true&category_id=1&service_type_id=2&search=repair
     */
    public function index(Request $request)
    {
        $query = Service::with(['category', 'serviceTypes'])
            ->withCount('scopeOfWorks');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->boolean('featured')) {
            $query->featured();
        }

        if ($request->filled('category_id')) {
            $query->byCategory($request->category_id);
        }

        if ($request->filled('service_type_id')) {
            $query->byServiceType($request->service_type_id);
        }

        if ($request->filled('search')) {
            $query->search($request->search);
        }

        $services = $query->orderBy('order')->get();

        return response()->json([
            'success' => true,
            'message' => 'Services fetched successfully.',
            'data'    => $services,
        ]);
    }

    /**
     * Store a newly created service.
     * POST /api/services
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category_id'       => 'nullable|exists:categories,id',
            'name'              => 'required|string|max:255|unique:services,name,NULL,id,deleted_at,NULL',
            'subtitle'          => 'nullable|string|max:255',
            'description'       => 'required|string|max:500',
            'long_description'  => 'nullable|string',
            'icon'              => 'nullable|string|max:100',
            'status'            => 'sometimes|in:active,inactive,draft',
            'featured'          => 'sometimes|boolean',
            'order'             => 'sometimes|integer|min:0',
            'image'             => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'gallery_images'    => 'nullable|array',
            'gallery_images.*'  => 'image|mimes:jpg,jpeg,png,webp|max:2048',
            'service_type_ids'  => 'nullable|array',
            'service_type_ids.*'=> 'exists:service_types,id',
            'scope_of_works'    => 'nullable|array',
            'scope_of_works.*'  => 'string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();

        // Handle main image upload
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('services/images', 'public');
        }

        // Handle gallery uploads
        if ($request->hasFile('gallery_images')) {
            $data['gallery_images'] = collect($request->file('gallery_images'))
                ->map(fn($file) => $file->store('services/gallery', 'public'))
                ->toArray();
        }

        // Auto-generate slug
        $data['slug'] = $this->uniqueSlug(Str::slug($data['name']));

        // Defaults
        $data['status']   = $data['status']   ?? 'active';
        $data['featured'] = $data['featured']  ?? false;
        $data['order']    = $data['order']     ?? 0;

        $service = Service::create($data);

        if ($request->has('service_types')) {
            $this->syncServiceTypesAndScopes($service, $request->service_types);
        }

        // Attach service types
        if (!empty($data['service_type_ids'])) {
            $service->attachServiceTypes($data['service_type_ids']);
        }

        // Add scope of works
        if (!empty($data['scope_of_works'])) {
            $service->updateScopeOfWorks($data['scope_of_works']);
        }

        return response()->json([
            'success' => true,
            'message' => 'Service created successfully.',
            'data'    => $service->load(['category', 'serviceTypes', 'scopeOfWorks']),
        ], 201);
    }

    /**
     * Display a single service.
     * GET /api/services/{id}
     */
    public function show(string $id)
    {
        $service = Service::with(['category', 'serviceTypes', 'scopeOfWorks'])
            ->find($id);

        if (!$service) {
            return response()->json([
                'success' => false,
                'message' => 'Service not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Service fetched successfully.',
            'data'    => $service,
        ]);
    }

    /**
     * Update an existing service.
     * PUT/PATCH /api/services/{id}
     */
    public function update(Request $request, string $id)
    {
        $service = Service::find($id);

        if (!$service) {
            return response()->json([
                'success' => false,
                'message' => 'Service not found.',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'category_id'       => 'nullable|exists:categories,id',
            'name'              => "sometimes|required|string|max:255|unique:services,name,{$id}",
            'subtitle'          => 'nullable|string|max:255',
            'description'       => 'sometimes|required|string|max:500',
            'long_description'  => 'nullable|string',
            'icon'              => 'nullable|string|max:100',
            'status'            => 'sometimes|in:active,inactive,draft',
            'featured'          => 'sometimes|boolean',
            'order'             => 'sometimes|integer|min:0',
            'image'             => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'gallery_images'    => 'nullable|array',
            'gallery_images.*'  => 'image|mimes:jpg,jpeg,png,webp|max:2048',
            'service_type_ids'  => 'nullable|array',
            'service_type_ids.*'=> 'exists:service_types,id',
            'scope_of_works'    => 'nullable|array',
            'scope_of_works.*'  => 'string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();

        // Replace main image
        if ($request->hasFile('image')) {
            if ($service->image) {
                Storage::disk('public')->delete($service->image);
            }
            $data['image'] = $request->file('image')->store('services/images', 'public');
        }

        // Replace gallery
        if ($request->hasFile('gallery_images')) {
            if (!empty($service->gallery_images)) {
                foreach ($service->gallery_images as $old) {
                    Storage::disk('public')->delete($old);
                }
            }
            $data['gallery_images'] = collect($request->file('gallery_images'))
                ->map(fn($file) => $file->store('services/gallery', 'public'))
                ->toArray();
        }

        // Re-generate slug only if name changed
        if (isset($data['name']) && $data['name'] !== $service->name) {
            $data['slug'] = $this->uniqueSlug(Str::slug($data['name']), $service->id);
        }

        $service->update($data);

        if ($request->has('service_types')) {
            $this->syncServiceTypesAndScopes($service, $request->service_types);
        }

        // Sync service types (if provided)
        if (array_key_exists('service_type_ids', $data)) {
            $service->attachServiceTypes($data['service_type_ids'] ?? []);
        }

        // Replace scope of works (if provided)
        if (array_key_exists('scope_of_works', $data)) {
            $service->updateScopeOfWorks($data['scope_of_works'] ?? []);
        }

        return response()->json([
            'success' => true,
            'message' => 'Service updated successfully.',
            'data'    => $service->load(['category', 'serviceTypes', 'scopeOfWorks']),
        ]);
    }

    /**
     * Soft delete a service.
     * DELETE /api/services/{id}
     */
    public function destroy(string $id)
    {
        $service = Service::find($id);

        if (!$service) {
            return response()->json([
                'success' => false,
                'message' => 'Service not found.',
            ], 404);
        }

        $service->delete(); // soft delete via SoftDeletes trait

        return response()->json([
            'success' => true,
            'message' => 'Service deleted successfully.',
        ]);
    }

    /**
     * Restore a soft-deleted service.
     * PATCH /api/services/{id}/restore
     */
    public function restore(string $id)
    {
        $service = Service::onlyTrashed()->find($id);

        if (!$service) {
            return response()->json([
                'success' => false,
                'message' => 'Deleted service not found.',
            ], 404);
        }

        $service->restore();

        return response()->json([
            'success' => true,
            'message' => 'Service restored successfully.',
            'data'    => $service,
        ]);
    }

    /**
     * Public listing — active services formatted for the frontend.
     * GET /api/services
     */
    public function publicIndex()
    {
        $services = Service::with(['category', 'serviceTypes', 'scopeOfWorks'])
            ->active()
            ->orderBy('order')
            ->get()
            ->map(fn($service) => $this->formatPublicService($service));

        return response()->json([
            'success' => true,
            'message' => 'Services fetched successfully.',
            'data'    => $services,
        ]);
    }

    /**
     * Public single service by slug.
     * GET /api/services/{slug}
     */
    public function publicShow(string $slug)
    {
        $service = Service::with(['category', 'serviceTypes', 'scopeOfWorks'])
            ->active()
            ->where('slug', $slug)
            ->first();

        if (!$service) {
            return response()->json([
                'success' => false,
                'message' => 'Service not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Service fetched successfully.',
            'data'    => $this->formatPublicService($service),
        ]);
    }

    /**
     * Shared formatter for public responses.
     */
    private function formatPublicService(Service $service): array
    {
        return [
            'id'          => $service->id,
            'name'        => $service->name,
            'slug'        => $service->slug,
            'subtitle'    => $service->subtitle,
            'description' => $service->description,
            'icon'        => $service->icon,
            'image'       => $service->image ? asset('storage/' . $service->image) : null,
            'featured'    => $service->featured,
            'category'    => $service->category?->name ?? 'Uncategorized',
            'services'    => $service->serviceTypes->map(fn($type) => [
                'type'        => $type->name,
                'color'       => $type->color,
                'scopeOfWork' => $service->scopeOfWorks
                    ->where('service_type_id', $type->id) // ✅ grouped by type
                    ->where('status', 'active')
                    ->sortBy('order')
                    ->pluck('description')
                    ->values(),
            ])->values(),
        ];
    }
   
    private function syncServiceTypesAndScopes(Service $service, array $serviceTypes): void
    {
    // Remove old scopes
    $service->scopeOfWorks()->delete();

    $typeIds = [];
    foreach ($serviceTypes as $typeData) {
        $typeId    = $typeData['id']     ?? null;
        $scopes    = $typeData['scopes'] ?? [];

        if (!$typeId) continue;
        $typeIds[] = $typeId;

        foreach ($scopes as $order => $description) {
            if (!trim($description)) continue;
            $service->scopeOfWorks()->create([
                'service_type_id' => $typeId,
                'description'     => trim($description),
                'order'           => $order,
                'status'          => 'active',
            ]);
        }
    }

    // Sync pivot table
    $service->attachServiceTypes($typeIds);
    }

    private function uniqueSlug(string $base, ?int $ignoreId = null): string
    {
        $slug      = $base;
        $counter   = 1;

        while (
            Service::withoutTrashed()
                ->where('slug', $slug)
                ->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = "{$base}-{$counter}";
            $counter++;
        }

        return $slug;
    }
}