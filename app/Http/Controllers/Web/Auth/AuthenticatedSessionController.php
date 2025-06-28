<?php

namespace App\Http\Controllers\Web\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Models\SystemSetting;


class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        $systemSetting = SystemSetting::first();
        return view('auth.layouts.login', compact('systemSetting'));
    }
    /**
     * Handle an incoming authentication request.
     */
    // public function store(LoginRequest $request): RedirectResponse
    // {
    //     // Authenticate the user
    //     $request->authenticate();

    //     // Retrieve the authenticated user
    //     $user = auth()->user();

    //     // if ($user->status == 'inactive') {
    //     //     Auth::guard('web')->logout();
    //     //     $request->session()->invalidate();

    //     //     $request->session()->regenerateToken();
    //     //     flash()->error('your account is not active.');
    //     //     return redirect('/login');
    //     // }
    //     // Check the user's role
    //     if ($user->role === 'admin') {
    //         flash()->success('Login successfully.');
    //         // Redirect to admin dashboard
    //         return redirect()->route('admin.dashboard');
    //     }
    //     $request->session()->regenerate();
    //     return redirect()->intended(route('admin.dashboard', absolute: false));
    // }

    // /**
    //  * Destroy an authenticated session.
    //  */
    // public function destroy(Request $request): RedirectResponse
    // {
    //     Auth::guard('web')->logout();

    //     $request->session()->invalidate();

    //     $request->session()->regenerateToken();
    //     // flash()->success('Logout successfully.');
    //     return redirect('/');
    // }


    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        if (Auth::user()->role !== 'admin') {
            Auth::logout();
            return redirect()->route('login')->withErrors(['email' => 'Unauthorized']);
        }

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
