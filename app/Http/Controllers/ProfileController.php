<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\DeleteAccountCode;
use App\Notifications\DeleteAccountVerification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $request->user()->id],
        ]);

        $request->user()->fill([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    public function requestDeleteCode(Request $request)
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = User::find(Auth::id());

        if (!$user) {
            return back()->withErrors(['error' => 'Lietotājs nav atrasts.']);
        }

        // Generate a random 6-digit code
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Delete any existing codes for this user
        DeleteAccountCode::where('user_id', $user->id)->delete();

        // Create new verification code
        DeleteAccountCode::create([
            'user_id' => $user->id,
            'code' => $code,
            'expires_at' => Carbon::now()->addMinutes(15),
        ]);

        // Send verification code email
        $user->notify(new DeleteAccountVerification($code));

        return back()->with('status', 'Verifikācijas kods ir nosūtīts uz jūsu e-pastu.');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    /**
     * Display the user's profile.
     */
    public function show(Request $request)
    {
        return view('profile', [
            'user' => $request->user(),
        ]);
    }
}
