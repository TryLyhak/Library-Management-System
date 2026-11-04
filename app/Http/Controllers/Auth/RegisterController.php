<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class RegisterController extends Controller
{
    // Show the registration form
    public function create(): View
    {
        return view('auth.register');
    }

    // Validate the form, create the account, and log the new user in
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            // "confirmed" checks that password_confirmation has the same value
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        // The password is hashed automatically by the User model's "hashed" cast
        $user = User::create($validated);

        // Log the new user in right away
        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('dashboard');
    }
}
