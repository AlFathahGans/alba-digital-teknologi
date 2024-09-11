<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;

use App\Events\EmailVerified; // Import event

class VerificationController extends Controller
{
    public function verify(Request $request, $id, $hash): RedirectResponse
    {
        Log::info('Verification process started for user ID: ' . $id);

        $user = User::findOrFail($id);

        // Log email hash for debugging
        Log::info('User email hash: ' . sha1($user->getEmailForVerification()));
        Log::info('Provided hash: ' . $hash);

        if (!hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            Log::warning('Email hash mismatch for user ID: ' . $id);
            return redirect('/email-verification-error');
        }

        if ($user->hasVerifiedEmail()) {
            Log::info('User ID ' . $id . ' already verified.');
            return redirect('/email-already-verified');
        }

        if ($user->markEmailAsVerified()) {
            Log::info('User ID ' . $id . ' email marked as verified.');
            event(new EmailVerified($user)); // Memicu event setelah email diverifikasi
        }

        Log::info('User verification status: ' . $user->hasVerifiedEmail());

        Auth::login($user);
        Log::info('User ID ' . $id . ' logged in successfully.');

        return redirect('/email-verified');
    }
}
