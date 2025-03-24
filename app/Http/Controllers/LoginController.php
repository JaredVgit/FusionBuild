<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Accounts;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function login()
    {
    return view('login');
    }

    public function LoginVerification(Request $request)
{
    $request->validate([
        'email' => 'required|string|email',
        'password' => 'required|string',
    ]);

    $user = Accounts::where('email', $request->input('email'))->first();

    // Check if user exists
    if ($user) {
        if (Hash::check($request->input('password'), $user->password)) {

            // Check user status
            if ($user->user_status !== 'active') {
                return redirect()->route('login')
                                 ->withErrors(['login_error' => 'Invalid email or password'])
                                 ->withInput();
            }
            // Log the user in
            Auth::login($user);

            // Set the first name in the session
            $request->session()->put('firstname', $user->firstname);

            // Log login success
            Log::info('User logged in: ' . $user->email);

            return redirect()->intended(route('Dashboard'));
        } else {
            // Invalid password
            return redirect()->route('login')
                             ->withErrors(['login_error' => 'Invalid email or password'])
                             ->withInput();
        }

    } else {
        // User not found
        return redirect()->route('login')
                         ->withErrors(['login_error' => 'Invalid email or password'])
                         ->withInput();
    }
}

    public function logout(Request $request)
{
    $user = Auth::user();

    // Perform logout operations
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    // Prevent caching of the page
    $response = redirect('/login')->with('success', 'You have been logged out successfully.');
    $response->headers->add([
        'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
        'Cache-Control' => 'post-check=0, pre-check=0',
        'Pragma' => 'no-cache',
    ]);

    return $response;
}

public function index()
    {
        return view ('welcome');
    }


}
