<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    /**
     * List all categories.
     * GET /api/admin/categories?status=active&with_services=true&search=hardware
     */
    public function index(Request $request)
    {
        $query = Category::withCount([
            'services as services_count',
            'services as active_services_count' => fn($q) => $q->where('status', 'active'),
        ]);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->boolean('with_services')) {
            $query->withActiveServices();
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('description', 'like', "%{$request->search}%");
            });
        }

        $categories = $query->ordered()->get();

        return response()->json([
            'success' => true,
            'message' => 'Categories fetched successfully.',
            'data'    => $categories,
        ]);
    }

    /**
     * Create a new category.
     * POST /api/admin/categories
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'        => 'required|string|max:255|unique:categories,name,NULL,id,deleted_at,NULL',
            'description' => 'nullable|string|max:500',
            'icon'        => 'nullable|string|max:100',
            'image'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'status'      => 'sometimes|in:active,inactive',
            'order'       => 'sometimes|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('categories/images', 'public');
        }

        $data['slug']   = Str::slug($data['name']);
        $data['status'] = $data['status'] ?? 'active';
        $data['order']  = $data['order']  ?? 0;

        $category = Category::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Category created successfully.',
            'data'    => $category,
        ], 201);
    }

    /**
     * Show a single category with its active services.
     * GET /api/admin/categories/{id}
     */
    public function show(string $id)
    {
        $category = Category::withCount([
                'services as services_count',
                'services as active_services_count' => fn($q) => $q->where('status', 'active'),
            ])
            ->with(['services' => fn($q) => $q->where('status', 'active')->orderBy('order')])
            ->find($id);

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Category fetched successfully.',
            'data'    => $category,
        ]);
    }

    /**
     * Update a category.
     * PUT /api/admin/categories/{id}
     */
    public function update(Request $request, string $id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found.',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name'        => "sometimes|required|string|max:255|unique:categories,name,{$id}",
            'description' => 'nullable|string|max:500',
            'icon'        => 'nullable|string|max:100',
            'image'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'status'      => 'sometimes|in:active,inactive',
            'order'       => 'sometimes|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();

        if ($request->hasFile('image')) {
            if ($category->image) {
                Storage::disk('public')->delete($category->image);
            }
            $data['image'] = $request->file('image')->store('categories/images', 'public');
        }

        // Signal from frontend to remove image without replacing it
        if ($request->boolean('remove_image')) {
            if ($category->image) {
                Storage::disk('public')->delete($category->image);
            }
            $data['image'] = null;
        }

        if (isset($data['name']) && $data['name'] !== $category->name) {
            $data['slug'] = $this->uniqueSlug(Str::slug($data['name']));
        }

        $category->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Category updated successfully.',
            'data'    => $category->loadCount([
                'services as services_count',
                'services as active_services_count' => fn($q) => $q->where('status', 'active'),
            ]),
        ]);
    }

    /**
     * Soft delete a category.
     * DELETE /api/admin/categories/{id}
     */
    public function destroy(string $id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found.',
            ], 404);
        }

        if ($category->services()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete a category that has services assigned to it.',
            ], 409);
        }

        $category->delete();

        return response()->json([
            'success' => true,
            'message' => 'Category deleted successfully.',
        ]);
    }

    /**
     * Restore a soft-deleted category.
     * PATCH /api/admin/categories/{id}/restore
     */
    public function restore(string $id)
    {
        $category = Category::onlyTrashed()->find($id);

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Deleted category not found.',
            ], 404);
        }

        $category->restore();

        return response()->json([
            'success' => true,
            'message' => 'Category restored successfully.',
            'data'    => $category,
        ]);
    }

    private function uniqueSlug(string $base, ?int $ignoreId = null): string
    {
        $slug    = $base;
        $counter = 1;

        while (
            Category::withoutTrashed()
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