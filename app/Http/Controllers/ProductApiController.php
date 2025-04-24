<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductImage;
use Storage;

class ProductApiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // List products with search and pagination
    public function index(Request $request)
    {
        $query = Product::with(['category', 'images']);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('description', 'like', "%$search%");
            });
        }

        $products = $query->orderByDesc('created_at')->paginate(10);

        return response()->json($products);

        // \Log::info('User:', ['user' => auth()->user()]);
        // return response()->json(Category::all());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    // Store a new product
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'required|string',
            'date_time' => 'required|date',
            'images.*' => 'image|max:2048',
        ]);

        $product = Product::create($validated);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $img) {
                $path = $img->store('products', 'public');
                $product->images()->create(['image_path' => $path]);
            }
        }

        return response()->json(['message' => 'Product created', 'product' => $product->load('category', 'images')], 201);
    }

    /**
     * Display the specified resource.
     */
    // Show a single product
    public function show(Product $product)
    {
        return response()->json($product->load('category', 'images'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    // Update a product
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'category_id' => 'sometimes|required|exists:categories,id',
            'description' => 'sometimes|required|string',
            'date_time' => 'sometimes|required|date',
            'images.*' => 'image|max:2048',
        ]);

        $product->update($validated);

        // Delete images not in existing_image_id
        $existingIds = $request->input('existing_image_ids', []);
        $product->images()
            ->whereNotIn('id', $existingIds)
            ->each(function($image) {
                // Optionally: delete the file from storage
                \Storage::disk('public')->delete($image->image_path);
                $image->delete();
            });

        // Handle images if present
        if ($request->hasFile('images')) {
            // Optionally, remove old images here
            foreach ($request->file('images') as $img) {
                $path = $img->store('products', 'public');
                $product->images()->create(['image_path' => $path]);
            }
        }

        return response()->json(['message' => 'Product updated', 'product' => $product->load('category', 'images')]);
    }

    /**
     * Remove the specified resource from storage.
     */
    // Delete a product
    public function destroy(Product $product)
    {
        // Optionally, delete images from storage here
        $product->delete();
        return response()->json(['message' => 'Product deleted']);
    }
}
