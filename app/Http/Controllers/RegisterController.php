<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use App\Mail\OtpMail;
use App\Models\Accounts;


class RegisterController extends Controller
{
    public function register()
    {
    return view('register');
    }

    public function registration(Request $request)
{
    // Validate the input
    $validator = Validator::make($request->all(), [
    'firstname' => 'required|string|max:255',
    'lastname' => 'required|string|max:255',
    'email' => 'required|email|max:255',
    'password' => [
        'required',
        'string',
        'min:8',
        'confirmed',
        //'regex:/[a-zA-Z]/',  // At least one alphabetic character
        //'regex:/[0-9]/',      // At least one numeric character
    ],
    'password_confirmation' => 'required|string|min:8',
]);


    if ($validator->fails()) {
        return redirect()->back()
                         ->withErrors($validator)
                         ->withInput();
    }

    // Check if the email already exists in the database
    $user = Accounts::where('email', $request->email)->first();

    if ($user) {
        if ($user->user_status === 'active') {
            // If the email is already registered and active
            return redirect()->back()
                             ->withErrors(['email' => 'This email is already registered.'])
                             ->withInput();
        } elseif ($user->user_status === 'inactive') {
            // If the email is registered but inactive, overwrite the existing record
            // Generate a new OTP
            $otp = random_int(100000, 999999);

            // Hash the new OTP
            $hashedOtp = Hash::make($otp);

            // Update the existing user record
            $user->firstname = $request->firstname;
            $user->lastname = $request->lastname;
            $user->password = Hash::make($request->password);
            $user->otp = $hashedOtp; // Update OTP
            $user->user_status = 'inactive'; // Ensure status is set to inactive

            $user->save();

            // Store email in session
            $request->session()->put('email', $request->email);

            // Send OTP email
            Mail::to($request->email)->send(new OtpMail($otp));

            // Redirect to the verification form
            return redirect()->route('ShowRegistrationVerification');
        }
    } else {
        // If the email is not registered, proceed with creating a new user
        // Generate a new OTP
        $otp = random_int(100000, 999999);

        // Hash the OTP
        $hashedOtp = Hash::make($otp);

        // Create a new user record
        $register = new Accounts;

        $register->firstname = $request->firstname;
        $register->lastname = $request->lastname;
        $register->email = $request->email;
        $register->password = Hash::make($request->password);
        $register->otp = $hashedOtp; // Store hashed OTP
        $register->user_status = 'inactive'; // Default status

        $register->save();

        // Store email in session
        $request->session()->put('email', $request->email);

        // Send OTP email
        Mail::to($request->email)->send(new OtpMail($otp));

        // Redirect to the verification form
        return redirect()->route('ShowRegistrationVerification');
    }
}

public function ShowRegistrationVerification()
{
    return view('ShowRegistrationVerification');
}

public function RegistrationVerification(Request $request)
{
    $validator = Validator::make($request->all(), [
        'code' => 'required|numeric|digits:6',
    ]);

    if ($validator->fails()) {
        return redirect()->route('ShowRegistrationVerification')
                         ->withErrors($validator)
                         ->withInput();
    }

    // Find the user by the email address that was used to register
    $user = Accounts::where('email', $request->session()->get('email'))->first();

    if (!$user) {
        return redirect()->route('ShowRegistrationVerification')
                         ->withErrors(['email' => 'User not found.'])
                         ->withInput();
    }

    // Verify the OTP
    if (!Hash::check($request->code, $user->otp)) {
        return redirect()->route('ShowRegistrationVerification')
                         ->withErrors(['code' => 'Invalid Code.'])
                         ->withInput();
    }

    // Mark the user as verified
    $user->user_status = 'active'; // set user_status as active
    $user->otp = null; // Clear OTP after verification
    $user->email_verified_at = now(); // set the email_verified_at to current timestamp
    $user->save();
    $request->session()->forget('email');

    return redirect()->route('login') // Redirect to login or another route
                     ->with('success', 'Registration Successful!');
}


    public function ResendVerification(Request $request)
{
    // Retrieve the email from the session
    $email = $request->session()->get('email');

    if (!$email) {
        return redirect()->route('ShowRegistrationVerification')
                         ->withErrors(['email' => 'No email address found.'])
                         ->withInput();
    }

    // Find the user by the email address
    $user = Accounts::where('email', $email)->first();

    if (!$user) {
        return redirect()->route('ShowRegistrationVerification')
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
    Mail::to($email)->send(new OtpMail($newOtp));

    // Redirect back with a success message
    return redirect()->route('ShowRegistrationVerification')
                     ->with('success', 'A new Code has been sent to your email.');
}
}
