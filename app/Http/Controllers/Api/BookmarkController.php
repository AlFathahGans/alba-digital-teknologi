<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Bookmark;
use App\Models\Post;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Exception;
use Illuminate\Support\Facades\Cache;

class BookmarkController extends Controller
{
    // Menampilkan daftar bookmark user
    public function index()
    {
        try {
            $userId = auth()->id();
            $cacheKey = "user:{$userId}:bookmarks";

            $bookmarks = Cache::remember($cacheKey, 600, function () use ($userId) {
                return auth()->user()->bookmarks()->with('user', 'post')->get();
            });

            return response()->json($bookmarks);
        } catch (Exception $e) {
            return response()->json(['message' => 'Error fetching bookmarks', 'error' => $e->getMessage()], 500);
        }
    }

    // Menyimpan bookmark baru
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'post_id' => 'required|exists:posts,id',
            ]);

            // Cek apakah bookmark sudah ada
            $existingBookmark = auth()->user()->bookmarks()->where('post_id', $validated['post_id'])->first();
            if ($existingBookmark) {
                return response()->json(['message' => 'Bookmark already exists'], 409);
            }

            $bookmark = auth()->user()->bookmarks()->create([
                'post_id' => $validated['post_id'],
            ]);

            // Hapus cache jika ada
            $cacheKey = "user:{$bookmark->user_id}:bookmarks";
            Cache::forget($cacheKey);

            return response()->json($bookmark, 201);
        } catch (ValidationException $e) {
            return response()->json(['message' => 'Validation failed', 'errors' => $e->errors()], 422);
        } catch (Exception $e) {
            return response()->json(['message' => 'Error creating bookmark', 'error' => $e->getMessage()], 500);
        }
    }

    // Menampilkan detail bookmark berdasarkan ID
    public function show($id)
    {
        try {
            $cacheKey = "bookmark:{$id}";
            $bookmark = Cache::remember($cacheKey, 600, function () use ($id) {
                return Bookmark::where('id', $id)->where('user_id', auth()->id())->with('post')->firstOrFail();
            });

            return response()->json($bookmark);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Bookmark not found'], 404);
        } catch (Exception $e) {
            return response()->json(['message' => 'Error fetching bookmark', 'error' => $e->getMessage()], 500);
        }
    }

    // Update bookmark
    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'post_id' => 'sometimes|exists:posts,id',
                // Jika ada field lain yang ingin diperbarui, tambahkan di sini
            ]);

            $bookmark = Bookmark::where('id', $id)->where('user_id', auth()->id())->firstOrFail();

            if (isset($validated['post_id'])) {
                // Pastikan post_id valid jika disertakan dalam update
                $bookmark->post_id = $validated['post_id'];
            }

            $bookmark->save();

            // Hapus cache terkait bookmark yang sudah diperbarui
            $cacheKey = "bookmark:{$id}";
            Cache::forget($cacheKey);

            // Hapus cache daftar bookmark user
            $userId = auth()->id();
            $userCacheKey = "user:{$userId}:bookmarks";
            Cache::forget($userCacheKey);

            return response()->json($bookmark);
        } catch (ValidationException $e) {
            return response()->json(['message' => 'Validation failed', 'errors' => $e->errors()], 422);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Bookmark not found'], 404);
        } catch (Exception $e) {
            return response()->json(['message' => 'Error updating bookmark', 'error' => $e->getMessage()], 500);
        }
    }

    // Menghapus bookmark
    public function destroy($id)
    {
        try {
            $bookmark = Bookmark::where('id', $id)->where('user_id', auth()->id())->firstOrFail();
            $bookmark->delete();

            // Hapus cache terkait bookmark yang dihapus
            $cacheKey = "bookmark:{$id}";
            Cache::forget($cacheKey);

            // Hapus cache daftar bookmark user
            $userId = auth()->id();
            $userCacheKey = "user:{$userId}:bookmarks";
            Cache::forget($userCacheKey);

            return response()->json(['message' => 'Bookmark deleted successfully']);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Bookmark not found'], 404);
        } catch (Exception $e) {
            return response()->json(['message' => 'Error deleting bookmark', 'error' => $e->getMessage()], 500);
        }
    }
}
