<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

use Illuminate\Http\RedirectResponse;

class VerificationController extends Controller
{
    public function verify(Request $request, $id, $hash): RedirectResponse
    {
        $user = User::findOrFail($id);
    
        if (!hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            // return response()->json(['message' => 'Invalid verification link'], 400);
            return redirect('/email-verification-error');
        }
    
        if ($user->hasVerifiedEmail()) {
            // return response()->json([
            //     'message' => 'Email already verified'
            // ], 200);
            return redirect('/email-already-verified');
        }
    
        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }
    
        // return response()->json([
        //     'message' => 'Email verified successfully'
        // ], 200);

         // Login otomatis setelah verifikasi berhasil
        Auth::login($user);

        return redirect('/email-verified'); 
    }
    

}

