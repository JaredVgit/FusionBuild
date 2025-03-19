<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Accounts;
use Illuminate\Support\Facades\Validator;
use App\Mail\EmailUpdatedMail;
use Illuminate\Support\Facades\Mail;
use App\Mail\PasswordUpdateMail;

class ProfileController extends Controller
{
    public function Profile()
    {
    return view('user.Profile.profile');
    }

    public function ProfileUpdate()
    {
    return view('user.Profile.profileUpdate');
    }

    public function saveProfileUpdate(Request $request)
    {
        $user = Auth::user();

        // Validate input
        $validator = Validator::make($request->all(), [
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Update user details
        $user->firstname = ucfirst(strtolower($request->firstname));
        $user->lastname = ucfirst(strtolower($request->lastname));
        $user->save();

        return redirect()->route('Profile')->with('success', 'Profile updated successfully.');
    }

    public function showEmailUpdate()
    {
    return view('user.Profile.EmailUpdate');
    }

    public function EmailUpdate(Request $request)
{
    $request->validate([
        'password' => 'required'
    ]);

    // Check if the password is correct
    if (!Hash::check($request->password, auth()->user()->password)) {
        return back()->with('error', 'Incorrect password. Please try again.');
    }

    return view('user.Profile.NewEmailUpdate')->with('success', 'Your email has been updated successfully.');
}

public function showNewEmailUpdate()
{
return view('user.Profile.NewEmailUpdate');
}

public function NewEmailUpdate(Request $request)
{
    // Validate the new email
    $validator = Validator::make($request->all(), [
        'new_email' => 'required|email',
    ]);

    if ($validator->fails()) {
        return redirect()->route('showNewEmailUpdate')->withErrors($validator)->withInput();
    }

    // Check if the email exists in the accounts table
    $existingAccount = Accounts::where('email', $request->new_email)->first();

    if ($existingAccount) {
        if ($existingAccount->user_status === 'active') {
            return redirect()->route('showNewEmailUpdate')->with('error', 'This email is already taken.');
        } elseif ($existingAccount->user_status === 'inactive') {
            // Delete the inactive account record
            $existingAccount->delete();
        }
    }

    // Generate OTP
    $otp = random_int(100000, 999999);

    // Store OTP in the session
    session(['otp' => (string)$otp, 'new_email' => $request->new_email]);

    // Send OTP to the new email
    Mail::to($request->new_email)->send(new EmailUpdatedMail($otp));

    return redirect()->route('showNewEmailUpdateOTP')->with('success', 'A verification code has been sent to your new email.');
}

    public function showNewEmailUpdateOTP()
    {
    return view('user.Profile.NewEmailUpdateOTP');
    }

    public function NewEmailUpdateOTP(Request $request)
{
    // Validate the OTP input
    $request->validate([
        'otp' => 'required|string|size:6', // Ensure it's a string of size 6
    ]);

    // Check if OTP and new_email exist in the session
    if (!session()->has('otp') || !session()->has('new_email')) {
        return redirect()->back()->with('error', 'Code session has expired or is invalid. Please try again.');
    }

    // Check if the OTP matches the one stored in the session
    if ($request->otp === session('otp')) {
        try {
            // Update the user's email since OTP is verified
            $user = Auth::user();
            $user->email = session('new_email');
            $user->save();

            // Clear the OTP and email from session
            session()->forget(['otp', 'new_email']);

            return redirect()->route('Profile')->with('success', 'Email updated successfully.');

        } catch (\Exception $e) {
            // Handle any exception that occurs while saving the email
            return redirect()->back()->with('error', 'Failed to update email. Please try again.');
        }
    }

    // If OTP does not match
    return redirect()->back()->with('error', 'Invalid Code. Please try again.');
}



public function NewEmailUpdateOTPresend(Request $request)
{
    // Check if the email is stored in the session
    $new_email = session('new_email');
    if (!$new_email) {
        return redirect()->back()->with('error', 'No email address found. Please try again.');
    }

    // Generate a new OTP
    $otp = random_int(100000, 999999);
    
    // Store the new OTP in the session
    session(['otp' => (string)$otp]);

    // Send the new OTP to the email
    Mail::to($new_email)->send(new EmailUpdatedMail($otp));

    return redirect()->back()->with('success', 'Code has been resent to your new email.');
}



public function PasswordUpdateOTP(Request $request)
{
    $user = Auth::user(); // Get the authenticated user

    if (!$user) {
        return redirect()->back()->with('error', 'User not found.');
    }

    // Generate a new OTP
    $otp = random_int(100000, 999999);

    // Store OTP and expiration in session
    $request->session()->put('otp', $otp);
    $request->session()->put('otp_expires_at', now()->addMinutes(10)); // OTP expires in 5 minutes

    // Send OTP email
    Mail::to($user->email)->send(new PasswordUpdateMail($otp));

    return redirect()->route('showPasswordUpdateOTP')->with('success', 'A verification code has been sent to your email.');
}

public function showPasswordUpdateOTP()
{
    return view('user.Profile.PasswordUpdateOTP');
}

public function PasswordUpdateOTPresend(Request $request)
{
    $user = Auth::user();

    if (!$user) {
        return redirect()->back()->with('error', 'User not found.');
    }

    // Generate a new OTP
    $otp = random_int(100000, 999999);

    // Store new OTP and update expiration in session
    $request->session()->put('otp', $otp);
    $request->session()->put('otp_expires_at', now()->addMinutes(10));

    // Send new OTP via email
    Mail::to($user->email)->send(new PasswordUpdateMail($otp));

    return redirect()->back()->with('success', 'A new verification code has been sent to your email.');
}



public function PasswordUpdateOTPverify(Request $request)
{
    // Validate OTP input
    $request->validate([
        'otp' => 'required|string|size:6',
    ]);

    $user = Auth::user();

    if (!$user) {
        return redirect()->route('login')->with('error', 'You must be logged in.');
    }

    // Check if OTP exists in session
    if (!session()->has('otp') || !session()->has('otp_expires_at')) {
        return redirect()->route('showPasswordUpdateOTP')->with('error', 'Code session has expired. Please request a new one.');
    }

    // Check if OTP has expired
    if (now()->greaterThan(session('otp_expires_at'))) {
        session()->forget('otp');
        session()->forget('otp_expires_at');
        return redirect()->route('showPasswordUpdateOTP')->with('error', 'Your verification code has expired. Please request a new one.');
    }

    // Retrieve OTP from session
    $sessionOtp = session('otp');

    // Verify the OTP
    if ($request->otp == $sessionOtp) {
        session()->forget('otp');
        session()->forget('otp_expires_at');
        return redirect()->route('showPasswordUpdate')->with('success', 'OTP verified! You can now reset your password.');
    }

    return redirect()->back()->with('error', 'Invalid OTP. Please try again.');
}

public function showPasswordUpdate()
{
    return view('user.Profile.PasswordUpdate');
}

public function PasswordUpdate(Request $request)
{
    $request->validate([
    'current_password' => 'required',
    'new_password' => [
        'required',
        'string',
        'min:8',
        'confirmed',
        ],
    ]);

    $user = auth()->user(); // Get the authenticated user

    // Check if the current password is correct
    if (!Hash::check($request->current_password, $user->password)) {
        return redirect()->back()->withErrors(['current_password' => 'Current password is incorrect.']);
    }

    // Update the password
    $user->password = Hash::make($request->new_password);
    $user->save();

    return redirect()->route('Profile')->with('success', 'Password changed successfully.');
}


}
