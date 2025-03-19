@extends('layouts.app')

@section('title', 'OTP Verification - FusionBuild')

@section('content')
<div class="p-8 max-w-2xl mx-auto">
    <div class="bg-darkBlue text-white p-6 rounded-lg shadow-lg">
        
        <!-- Back Button -->
        <div class="flex justify-end mb-6">
            <a href="/New-Email-update" class="bg-mustardOrange text-black font-bold py-2 px-4 rounded-lg hover:bg-yellow-500 transition">
                ← Back
            </a>
        </div>

        <h2 class="text-2xl font-bold text-center text-mustardOrange mb-6">OTP Verification</h2>

        <!-- Success Message -->
        @if (session('success'))
            <p class="text-green-400 text-center mb-4">{{ session('success') }}</p>
        @endif

        <!-- Error Message -->
        @if (session('error'))
            <p class="text-red-400 text-center mb-4">{{ session('error') }}</p>
        @endif

        @error('otp')
            <p class="text-red-400 text-center mb-4">{{ $message }}</p>
        @enderror

        <!-- OTP Verification Form -->
        <form action="{{ route('NewEmailUpdateOTP') }}" method="POST" class="space-y-4">
            @csrf

            <!-- OTP Input -->
            <div>
                <label for="otp" class="block text-sm font-semibold text-gray-300">Enter OTP</label>
                <input type="text" id="otp" name="otp" maxlength="6" value="{{ old('otp') }}" 
                       class="w-full p-3 mt-1 rounded-lg bg-gray-100 border border-gray-600 text-black focus:ring-2 focus:ring-mustardOrange focus:outline-none text-center tracking-widest text-xl" required>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-center mt-6">
                <button type="submit" class="bg-mustardOrange text-black font-bold py-2 px-6 rounded-lg hover:bg-yellow-500 transition">
                    Verify OTP
                </button>
            </div>

            <!-- Resend OTP -->
            <div class="text-center mt-4">
                <p class="text-gray-300 text-sm">Didn't receive an OTP? 
                    <a href="{{ route('NewEmailUpdateOTPresend') }}" class="text-mustardOrange font-bold hover:underline">Resend</a>
                </p>
            </div>
        </form>
    </div>
</div>
@endsection
