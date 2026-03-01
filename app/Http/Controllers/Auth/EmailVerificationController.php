<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmailVerificationController extends Controller
{
    /**
     * Mark the given user's email as verified.
     *
     * @return \Illuminate\Http\Response
     */
    public function verify(Request $request)
    {
        if (! $request->hasValidSignature()) {
            return redirect()->route('login')->with('status', 'Invalid verification link.');
        }

        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return redirect()->route('login')->with('status', 'Your email is already verified.');
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        Auth::logout();

        return redirect()->route('login')->with('status', 'Your email has been verified. Please log in.');
    }
}
