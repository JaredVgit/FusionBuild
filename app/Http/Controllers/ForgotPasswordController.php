<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Accounts;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\ForgotPasswordMail;
use Illuminate\Support\Facades\Validator;

class ForgotPasswordController extends Controller
{
    public function ForgotPassword()
    {
    return view('ForgotPassword');
    }

    public function ForgotPasswordEmail(Request $request)
{
    $request->validate([
        'email' => 'required|email|exists:accounts,email',
    ]);

    $user = Accounts::where('email', $request->email)->first();

    if ($user) {
        // Check if the user's status is deactivated
        if ($user->user_status === 'inactive') {
            return redirect()->back()
                             ->withErrors(['email' => 'Please enter a valid email.'])
                             ->withInput();
        }

        else{// Generate a 6-digit OTP
        $otp = random_int(100000, 999999);

        // Hash the OTP
        $hashedOtp = Hash::make($otp);

        // Store the OTP in the user's record
        $user->otp = $hashedOtp;
        $user->save();

        // Send OTP email
        Mail::to($request->email)->send(new ForgotPasswordMail($otp));

        // Store email in the session
        $request->session()->put('email', $request->email);

        // Redirect to the OTP verification form
        return redirect()->route('ShowForgotPasswordOTP');
    }}
}

    public function ShowForgotPasswordOTP()
    {
    return view('ForgotPasswordOTP');
    }

    public function ForgotPasswordOTP(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|numeric|digits:6',
        ]);
    
        $user = Accounts::where('email', $request->email)->first();
    
        if (!$user || !Hash::check($request->otp, $user->otp)) {
            // Redirect back with errors and reflash email to the session
            return redirect()->back()->withErrors(['otp' => 'Invalid Verification Code.'])
                                    ->withInput();
        }
    
        // OTP is correct; store email and OTP in session
        $request->session()->put('reset_email', $request->email);
        $request->session()->put('reset_otp', $request->otp);
    
        // Show the reset password form
        return view('resetPassword', ['otp' => $request->otp]);
    }


    public function ForgotPasswordOTPresend(Request $request)
    {
        // Retrieve the email from the session
    $email = $request->session()->get('email');

    if (!$email) {
        return redirect()->route('showVerificationForm')
                         ->withErrors(['email' => 'No email address found.'])
                         ->withInput();
    }

    // Find the user by the email address
    $user = Accounts::where('email', $email)->first();

    if (!$user) {
        return redirect()->route('showVerificationForm')
                         ->withErrors(['email' => 'User not found.'])
                         ->withInput();
    }

    // Generate a new OTP
    $newOtp = random_int(100000, 999999);

    // Hash the new OTP
    $hashedOtp = Hash::make($newOtp);

    // Update the user's OTP in the database
    $user->otp = $hashedOtp;
    $user->save();

    // Send the new OTP via email
    Mail::to($email)->send(new ForgotPasswordMail($newOtp));

    // Redirect back with a success message
    return redirect()->route('ShowForgotPasswordOTP')
                     ->with('success', 'A new Code has been sent to your email.');
}



    public function ShowForgotPasswordReset()
{
    // Retrieve the email from session
    $email = session('reset_email');

    // Show the reset password form
    return view('resetPassword', ['email' => $email]);
}

public function ForgotPasswordReset(Request $request)
{
    // Extract data from the request
    $email = $request->session()->get('reset_email');
    $otp = $request->session()->get('reset_otp');
    $password = $request->input('password');
    $password_confirmation = $request->input('password_confirmation');

    // Define the validation rules
    $rules = [
    'otp' => 'required|numeric|digits:6',
    'password' => [
        'required',
        'string',
        'min:8',
        'confirmed',
    ],
    'password_confirmation' => [
        'required',
        'string',
    ],
];


    // Create a validator instance
    $validator = Validator::make($request->all(), $rules);

    // Check if validation fails
    if ($validator->fails()) {
        return redirect()->route('ShowForgotPasswordReset')
                         ->withErrors($validator)
                         ->withInput();
    }

    // Find the user by email
    $user = Accounts::where('email', $email)->first();

    // Check if the user exists and OTP is still valid
    if (!$user || !Hash::check($otp, $user->otp)) {

        return redirect()->route('reset-password')
                         ->withErrors(['otp' => 'Invalid Code or Code expired.']);
    }

    // Update the user's password
    $user->password = Hash::make($password);
    $user->otp = null; // Clear OTP after reset
    $user->save();

    // Clear the email and OTP from session after reset
    $request->session()->forget('reset_email');
    $request->session()->forget('reset_otp');

    // Redirect to login with success message
    return redirect()->route('login')
                     ->with('success', 'Your password has been reset successfully!');
}

    
}
