<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use App\Models\PostCategory;
use Exception;
use Illuminate\Support\Facades\Cache;

class PostCategoryController extends Controller
{
    // Menampilkan daftar kategori
    public function index()
    {
        try {
            $cacheKey = 'post_categories';

            $categories = Cache::remember($cacheKey, 600, function () {
                return PostCategory::all();
            });

            return response()->json($categories);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to fetch categories', 'details' => $e->getMessage()], 500);
        }
    }

    // Membuat kategori baru
    public function store(Request $request)
    {
        try {
            $request->validate(['name' => 'required|string|max:255']);
            $category = PostCategory::create($request->only('name'));

            // Hapus cache daftar kategori
            Cache::forget('post_categories');

            return response()->json($category, 201);
        } catch (ValidationException $e) {
            return response()->json(['error' => 'Validation failed', 'details' => $e->errors()], 422);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to create category', 'details' => $e->getMessage()], 500);
        }
    }

    // Menampilkan kategori berdasarkan ID
    public function show($id)
    {
        try {
            $cacheKey = "post_category:{$id}";
            $category = Cache::remember($cacheKey, 600, function () use ($id) {
                return PostCategory::findOrFail($id);
            });

            return response()->json($category);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Category not found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to fetch category', 'details' => $e->getMessage()], 500);
        }
    }

    // Update kategori
    public function update(Request $request, $id)
    {
        try {
            $category = PostCategory::findOrFail($id);
            $request->validate(['name' => 'required|string|max:255']);
            $category->update($request->only('name'));

            // Hapus cache terkait kategori yang diupdate
            Cache::forget("post_category:{$id}");
            Cache::forget('post_categories');

            return response()->json($category);
        } catch (ValidationException $e) {
            return response()->json(['error' => 'Validation failed', 'details' => $e->errors()], 422);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Category not found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to update category', 'details' => $e->getMessage()], 500);
        }
    }

    // Menghapus kategori
    public function destroy($id)
    {
        try {
            $category = PostCategory::findOrFail($id);
            $category->delete();

            // Hapus cache terkait kategori yang dihapus
            Cache::forget("post_category:{$id}");
            Cache::forget('post_categories');

            return response()->json(['message' => 'Category deleted successfully']);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Category not found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to delete category', 'details' => $e->getMessage()], 500);
        }
    }
}
