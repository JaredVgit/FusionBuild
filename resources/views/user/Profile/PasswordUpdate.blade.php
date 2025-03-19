@extends('layouts.app')

@section('title', 'Change Password - FusionBuild')

@section('content')
<div class="p-8 max-w-2xl mx-auto">
    <div class="bg-darkBlue text-white p-6 rounded-lg shadow-lg">
        
        <!-- Back Button -->
        <div class="flex justify-end mb-6">
            <a href="/Profile" class="bg-mustardOrange text-black font-bold py-2 px-4 rounded-lg hover:bg-yellow-500 transition">
                ← Back
            </a>
        </div>

        <h2 class="text-2xl font-bold text-center text-mustardOrange mb-6">Change Password</h2>

        <!-- Success Message -->
        @if (session('success'))
            <p class="text-green-400 text-center mb-4">{{ session('success') }}</p>
        @endif

        <!-- Error Message -->
        @if (session('error'))
            <p class="text-red-400 text-center mb-4">{{ session('error') }}</p>
        @endif

        @error('current_password')
            <p class="text-red-400 text-center mb-4">{{ $message }}</p>
        @enderror

        @error('new_password')
            <p class="text-red-400 text-center mb-4">{{ $message }}</p>
        @enderror

        @error('new_password_confirmation')
            <p class="text-red-400 text-center mb-4">{{ $message }}</p>
        @enderror

        <!-- Change Password Form -->
        <form action="{{ route('PasswordUpdate') }}" method="POST" class="space-y-4">
            @csrf

            <!-- Current Password -->
            <div>
                <label for="current_password" class="block text-sm font-semibold text-gray-300">Current Password</label>
                <input type="password" id="current_password" name="current_password" 
                       class="w-full p-3 mt-1 rounded-lg bg-gray-100 border border-gray-600 text-black focus:ring-2 focus:ring-mustardOrange focus:outline-none transition" required>
            </div>

            <!-- New Password -->
            <div>
                <label for="new_password" class="block text-sm font-semibold text-gray-300">New Password</label>
                <input type="password" id="new_password" name="new_password" 
                       class="w-full p-3 mt-1 rounded-lg bg-gray-100 border border-gray-600 text-black focus:ring-2 focus:ring-mustardOrange focus:outline-none transition" required>
            </div>

            <!-- Confirm New Password -->
            <div>
                <label for="new_password_confirmation" class="block text-sm font-semibold text-gray-300">Confirm New Password</label>
                <input type="password" id="new_password_confirmation" name="new_password_confirmation" 
                       class="w-full p-3 mt-1 rounded-lg bg-gray-100 border border-gray-600 text-black focus:ring-2 focus:ring-mustardOrange focus:outline-none transition" required>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-center mt-6">
                <button type="submit" class="bg-mustardOrange text-black font-bold py-2 px-6 rounded-lg hover:bg-yellow-500 transition">
                    Change Password
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
