<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Cache; // Import Cache facade

use App\Notifications\ResetPasswordNotification;

use App\Jobs\SendVerificationEmail; // Import job
use App\Notifications\WelcomeEmailNotification; // Import notifikasi

use Exception;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        try {
            // Validasi data yang diterima
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8|confirmed',
            ]);

            // Cek apakah email sudah terdaftar
            if (Cache::has('email_check_' . $request->email)) {
                return response()->json([
                    'message' => 'Email is already registered. Please login or use another email.'
                ], 409);
            }

            // Cek apakah email sudah terdaftar
            if (User::where('email', $request->email)->exists()) {
                Cache::put('email_check_' . $request->email, true, now()->addMinutes(10));
                return response()->json([
                    'message' => 'Email is already registered. Please login or use another email.'
                ], 409);
            }

            // Buat pengguna baru
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            // Dispatch job untuk mengirim email verifikasi
            dispatch(new SendVerificationEmail($user));

            // Kirim notifikasi selamat datang
            $user->notify(new WelcomeEmailNotification());

            // Buat token untuk pengguna
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'access_token' => $token,
                'token_type' => 'Bearer'
            ], 201);
        } catch (Exception $e) {
            // Tangani error jika terjadi kesalahan
            return response()->json([
                'message' => 'Registration failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function login(Request $request)
    {
        try {
            // Validasi data yang diterima
            $request->validate([
                'email' => 'required|string|email',
                'password' => 'required|string',
            ]);

            // Coba autentikasi pengguna
            if (!Auth::attempt($request->only('email', 'password'))) {
                return response()->json(['message' => 'Invalid Login'], 401);
            }

            $user = User::where('email', $request['email'])->firstOrFail();

            if (!$user->hasVerifiedEmail()) {
                return response()->json(['message' => 'Please verify your email before logging in.'], 403);
            }


            // Buat token untuk pengguna
            $token = Auth::user()->createToken('auth_token')->plainTextToken;

            return response()->json([
                'access_token' => $token,
                'token_type' => 'Bearer'
            ], 200);
        } catch (Exception $e) {
            // Tangani error jika terjadi kesalahan
            return response()->json([
                'message' => 'Login failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function logout(Request $request)
    {
        try {
            $user = Auth::user();
            if ($user) {
                // Hapus semua token milik pengguna yang sedang login
                $user->tokens()->delete();
            }

            // Hapus sesi pengguna
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return response()->json(['message' => 'Successfully logged out'], 200);
        } catch (Exception $e) {
            // Tangani error jika terjadi kesalahan
            return response()->json([
                'message' => 'Logout failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function forgotPassword(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $cacheKey = 'user_exists_' . $request->email;
        $user = Cache::remember($cacheKey, now()->addMinutes(10), function() use ($request) {
            return User::where('email', $request->email)->first();
        });

        if (!$user) {
            return response()->json([
                'message' => 'Email not found.'
            ], 404);
        }

        // Generate password reset token
        $token = Password::createToken($user);

        // Send the notification
        $user->notify(new ResetPasswordNotification($token));

        return response()->json([
            'message' => 'Reset password link sent to your email.'
        ], 200);
    }

}
