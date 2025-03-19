@extends('layouts.app')

@section('title', 'Update Email - FusionBuild')

@section('content')
<div class="p-8 max-w-2xl mx-auto">
    <div class="bg-darkBlue text-white p-6 rounded-lg shadow-lg">
        
        <!-- Back to Profile Button -->
        <div class="flex justify-end mb-6">
            <a href="/Profile" class="bg-mustardOrange text-black font-bold py-2 px-4 rounded-lg hover:bg-yellow-500 transition">
                ← Profile
            </a>
        </div>

        <h2 class="text-2xl font-bold text-center text-mustardOrange mb-6">Password Verification</h2>

        <!-- Success Message -->
        @if (session('success'))
            <p class="text-green-400 text-center mb-4">{{ session('success') }}</p>
        @endif

        <!-- Error Message -->
        @if (session('error'))
            <p class="text-red-400 text-center mb-4">{{ session('error') }}</p>
        @endif

        <!-- Update Email Form -->
        <form action="{{ route('EmailUpdate') }}" method="POST" class="space-y-4">
            @csrf
            @method('POST')

            <!-- Password Confirmation -->
            <div>
                <label for="password" class="block text-sm font-semibold text-gray-300">Enter Password</label>
                <input type="password" id="password" name="password" 
                       class="w-full p-3 mt-1 rounded-lg bg-gray-100 border border-gray-600 text-black focus:ring-2 focus:ring-mustardOrange focus:outline-none transition" required>
                @error('password')
                    <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit Button -->
            <div class="flex justify-center mt-6">
                <button type="submit" class="bg-mustardOrange text-black font-bold py-2 px-6 rounded-lg hover:bg-yellow-500 transition">
                    Verify Password
                </button>
            </div>
        </form>
    </div>
</div>

@endsection
