<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Exception;
use Illuminate\Support\Facades\Cache;

class UserController extends Controller
{
    // Menampilkan daftar user
    public function index()
    {
        try {
            $cacheKey = 'users';
            $users = Cache::remember($cacheKey, 600, function () {
                return User::all();
            });
            return response()->json($users);
        } catch (Exception $e) {
            return response()->json(['message' => 'Error fetching users', 'error' => $e->getMessage()], 500);
        }
    }

    // Menyimpan user baru
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:8',
            ]);

            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
            ]);

            // Hapus cache daftar user
            Cache::forget('users');

            return response()->json($user, 201);
        } catch (ValidationException $e) {
            return response()->json(['message' => 'Validation failed', 'errors' => $e->errors()], 422);
        } catch (Exception $e) {
            return response()->json(['message' => 'Error creating user', 'error' => $e->getMessage()], 500);
        }
    }

    // Menampilkan user berdasarkan ID
    public function show($id)
    {
        try {
            $cacheKey = "user:{$id}";
            $user = Cache::remember($cacheKey, 600, function () use ($id) {
                return User::findOrFail($id);
            });
            return response()->json($user);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'User not found'], 404);
        } catch (Exception $e) {
            return response()->json(['message' => 'Error fetching user', 'error' => $e->getMessage()], 500);
        }
    }

    // Update user
    public function update(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);

            $validated = $request->validate([
                'name' => 'sometimes|string|max:255',
                'email' => 'sometimes|email|unique:users,email,' . $id,
                'password' => 'nullable|string|min:8',
            ]);

            $user->update([
                'name' => $validated['name'] ?? $user->name,
                'email' => $validated['email'] ?? $user->email,
                'password' => isset($validated['password']) ? Hash::make($validated['password']) : $user->password,
            ]);

            // Hapus cache user dan daftar user
            Cache::forget("user:{$id}");
            Cache::forget('users');

            return response()->json($user);
        } catch (ValidationException $e) {
            return response()->json(['message' => 'Validation failed', 'errors' => $e->errors()], 422);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'User not found'], 404);
        } catch (Exception $e) {
            return response()->json(['message' => 'Error updating user', 'error' => $e->getMessage()], 500);
        }
    }

    // Menghapus user
    public function destroy($id)
    {
        try {
            $user = User::findOrFail($id);
            $user->delete();

            // Hapus cache user dan daftar user
            Cache::forget("user:{$id}");
            Cache::forget('users');

            return response()->json(['message' => 'User deleted successfully']);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'User not found'], 404);
        } catch (Exception $e) {
            return response()->json(['message' => 'Error deleting user', 'error' => $e->getMessage()], 500);
        }
    }
}
