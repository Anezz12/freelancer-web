<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'avatar' => ['required', 'image', 'mimes:jpeg,png,jpg,gif,svg, webp'],
            'occupation' => ['required', 'string', 'max:255'],
            'role' => ['required', 'string', 'max:255', 'in:project_client,project_freelancer'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        if ($request->hasFile('avatar')) {
            $avatarPath = $request->file('avatar')->store('images', 'public');
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'occupation' => $request->occupation,
            'avatar' => $avatarPath,
            'connect' => 10,
            'password' => Hash::make($request->password),
        ]);

        $user->assignRole($request->role);

        $wallet = new Wallet([
            'balance' => 0,
        ]);
        $user->wallet()->save($wallet);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
