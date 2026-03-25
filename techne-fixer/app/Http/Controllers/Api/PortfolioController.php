<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Portfolio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class PortfolioController extends Controller
{
    /**
     * Public listing.
     * GET /api/portfolio
     */
    public function publicIndex()
    {
        $portfolios = Portfolio::published()
            ->ordered()
            ->get()
            ->map(fn($p) => $this->formatPublic($p));

        return response()->json([
            'success' => true,
            'message' => 'Portfolio fetched successfully.',
            'data'    => $portfolios,
        ]);
    }

    /**
     * Admin listing.
     * GET /api/admin/portfolio
     */
    public function index(Request $request)
    {
        $query = Portfolio::ordered();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', "%{$request->search}%")
                  ->orWhere('description', 'like', "%{$request->search}%");
            });
        }

        return response()->json([
            'success' => true,
            'message' => 'Portfolio fetched successfully.',
            'data'    => $query->get(),
        ]);
    }

    /**
     * Store a new portfolio item.
     * POST /api/admin/portfolio
     */
    public function store(Request $request)
    {
        if ($request->has('services') && is_string($request->services)) {
            $request->merge(['services' => json_decode($request->services, true)]);
        }

        $validator = Validator::make($request->all(), [
            'title'              => 'required|string|max:255',
            'description'        => 'required|string|max:500',
            'thumbnail'          => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'services'           => 'nullable|array',
            'services.*.name'    => 'required_with:services|string|max:100',
            'services.*.color'   => 'required_with:services|in:red,blue,yellow,green',
            'status'             => 'sometimes|in:published,draft',
            'order'              => 'sometimes|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();

        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = $request->file('thumbnail')->store('portfolio/thumbnails', 'public');
        }

        $data['status'] = $data['status'] ?? 'published';
        $data['order']  = $data['order']  ?? 0;

        $portfolio = Portfolio::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Portfolio item created successfully.',
            'data'    => $portfolio,
        ], 201);
    }

    /**
     * Show a single item.
     * GET /api/admin/portfolio/{id}
     */
    public function show(string $id)
    {
        $portfolio = Portfolio::find($id);

        if (!$portfolio) {
            return response()->json([
                'success' => false,
                'message' => 'Portfolio item not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Portfolio item fetched successfully.',
            'data'    => $portfolio,
        ]);
    }

    /**
     * Update a portfolio item.
     * PUT /api/admin/portfolio/{id}
     */
    public function update(Request $request, string $id)
    {
        $portfolio = Portfolio::find($id);

        if (!$portfolio) {
            return response()->json([
                'success' => false,
                'message' => 'Portfolio item not found.',
            ], 404);
        }

        if ($request->has('services') && is_string($request->services)) {
            $request->merge(['services' => json_decode($request->services, true)]);
        }

        $validator = Validator::make($request->all(), [
            'title'              => 'sometimes|required|string|max:255',
            'description'        => 'sometimes|required|string|max:500',
            'thumbnail'          => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'services'           => 'nullable|array',
            'services.*.name'    => 'required_with:services|string|max:100',
            'services.*.color'   => 'required_with:services|in:red,blue,yellow,green',
            'status'             => 'sometimes|in:published,draft',
            'order'              => 'sometimes|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();

        if ($request->hasFile('thumbnail')) {
            if ($portfolio->thumbnail) {
                Storage::disk('public')->delete($portfolio->thumbnail);
            }
            $data['thumbnail'] = $request->file('thumbnail')->store('portfolio/thumbnails', 'public');
        }

        if ($request->boolean('remove_thumbnail')) {
            if ($portfolio->thumbnail) {
                Storage::disk('public')->delete($portfolio->thumbnail);
            }
            $data['thumbnail'] = null;
        }

        $portfolio->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Portfolio item updated successfully.',
            'data'    => $portfolio->fresh(),
        ]);
    }

    /**
     * Soft delete.
     * DELETE /api/admin/portfolio/{id}
     */
    public function destroy(string $id)
    {
        $portfolio = Portfolio::find($id);

        if (!$portfolio) {
            return response()->json([
                'success' => false,
                'message' => 'Portfolio item not found.',
            ], 404);
        }

        $portfolio->delete();

        return response()->json([
            'success' => true,
            'message' => 'Portfolio item deleted successfully.',
        ]);
    }

    /**
     * Restore soft-deleted item.
     * PATCH /api/admin/portfolio/{id}/restore
     */
    public function restore(string $id)
    {
        $portfolio = Portfolio::onlyTrashed()->find($id);

        if (!$portfolio) {
            return response()->json([
                'success' => false,
                'message' => 'Deleted portfolio item not found.',
            ], 404);
        }

        $portfolio->restore();

        return response()->json([
            'success' => true,
            'message' => 'Portfolio item restored successfully.',
            'data'    => $portfolio,
        ]);
    }

    /**
     * Format for public response.
     */
    private function formatPublic(Portfolio $portfolio): array
    {
        return [
            'id'          => $portfolio->id,
            'title'       => $portfolio->title,
            'description' => $portfolio->description,
            'thumbnail'   => $portfolio->thumbnail
                ? asset('storage/' . $portfolio->thumbnail)
                : null,
            'services'    => $portfolio->services ?? [],
            'order'       => $portfolio->order,
        ];
    }
}