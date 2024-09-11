<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\PostCategory;
use App\Models\Bookmark;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Exception;
use Illuminate\Support\Facades\Cache;

class PostController extends Controller
{
    // Menampilkan semua post
    public function index()
    {
        try {
            $cacheKey = 'posts';
            $posts = Cache::remember($cacheKey, 600, function () {
                return Post::with('postCategory')->get()->map(function ($post) {
                    $post->image_url = $post->getFirstMediaUrl('images');
                    return $post;
                });
            });
            return response()->json($posts);
        } catch (Exception $e) {
            return response()->json(['message' => 'Error fetching posts', 'error' => $e->getMessage()], 500);
        }
    }

    // Menampilkan post yang belum di-bookmark oleh pengguna
    public function notBookmarked()
    {
        try {
            $userId = auth()->id();
            $bookmarkedPostIds = Bookmark::where('user_id', $userId)->pluck('post_id');
            $posts = Post::whereNotIn('id', $bookmarkedPostIds)->get()->map(function ($post) {
                $post->image_url = $post->getFirstMediaUrl('images');
                return $post;
            });
            return response()->json($posts);
        } catch (Exception $e) {
            return response()->json(['message' => 'Error fetching posts', 'error' => $e->getMessage()], 500);
        }
    }

    // Membuat post baru
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'content' => 'required|string',
                'post_category_id' => 'required|exists:post_categories,id',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);

            $validated['user_id'] = auth()->id();

            $post = Post::create($validated);

            if ($request->hasFile('image')) {
                $post->addMediaFromRequest('image')->toMediaCollection('images');
            }

            // Hapus cache terkait post
            Cache::forget('posts');

            return response()->json($post, 201);
        } catch (ValidationException $e) {
            return response()->json(['message' => 'Validation failed', 'errors' => $e->errors()], 422);
        } catch (Exception $e) {
            return response()->json(['message' => 'Error creating post', 'error' => $e->getMessage()], 500);
        }
    }

    // Menampilkan post berdasarkan ID
    public function show($id)
    {
        try {
            $cacheKey = "post:{$id}";
            $post = Cache::remember($cacheKey, 600, function () use ($id) {
                return Post::with('postCategory')->findOrFail($id);
            });
            $post->image_url = $post->getFirstMediaUrl('images');
            return response()->json($post);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Post not found'], 404);
        } catch (Exception $e) {
            return response()->json(['message' => 'Error fetching post', 'error' => $e->getMessage()], 500);
        }
    }

    // Update post
    public function update(Request $request, $id)
    {
        try {
            $post = Post::findOrFail($id);

            $validated = $request->validate([
                'title' => 'sometimes|string|max:255',
                'content' => 'sometimes|string',
                'post_category_id' => 'sometimes|exists:post_categories,id',
                'image' => 'sometimes|nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);

            $post->update($validated);

            if ($request->hasFile('image')) {
                $post->clearMediaCollection('images');
                $post->addMediaFromRequest('image')->toMediaCollection('images');
            }

            // Hapus cache terkait post
            Cache::forget("post:{$id}");
            Cache::forget('posts');

            return response()->json($post);
        } catch (ValidationException $e) {
            return response()->json(['message' => 'Validation failed', 'errors' => $e->errors()], 422);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Post not found'], 404);
        } catch (Exception $e) {
            return response()->json(['message' => 'Error updating post', 'error' => $e->getMessage()], 500);
        }
    }

    // Menghapus post
    public function destroy($id)
    {
        try {
            $post = Post::findOrFail($id);
            $post->delete();

            // Hapus cache terkait post
            Cache::forget("post:{$id}");
            Cache::forget('posts');

            return response()->json(['message' => 'Post deleted successfully']);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Post not found'], 404);
        } catch (Exception $e) {
            return response()->json(['message' => 'Error deleting post', 'error' => $e->getMessage()], 500);
        }
    }

    // Menghapus gambar
    public function deleteImage($id)
    {
        try {
            $post = Post::findOrFail($id);

            if ($post->hasMedia('images')) {
                $post->clearMediaCollection('images');
            }

            return response()->json(['message' => 'Image deleted successfully']);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Post not found'], 404);
        } catch (Exception $e) {
            return response()->json(['message' => 'Error deleting image', 'error' => $e->getMessage()], 500);
        }
    }
}
