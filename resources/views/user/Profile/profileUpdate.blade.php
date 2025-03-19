@extends('layouts.app')

@section('title', 'Update Profile - FusionBuild')

@section('content')
<div class="p-8 max-w-3xl mx-auto relative">
    <div class="bg-darkBlue text-white p-8 rounded-lg shadow-lg">
        
        <!-- Back to Profile Button -->
        <div class="flex justify-end mb-6">
            <a href="/Profile" class="bg-mustardOrange text-black font-bold py-2 px-4 rounded-lg hover:bg-yellow-500 transition">
                ← Profile
            </a>
        </div>

        <h2 class="text-2xl font-bold text-center text-mustardOrange mb-6">Update Profile</h2>

        <!-- Success Message -->
        @if (session('success'))
            <p class="text-green-400 text-center mb-4">{{ session('success') }}</p>
        @endif

        <!-- Update Profile Form -->
        <form action="{{ route('saveProfileUpdate') }}" method="POST" class="space-y-4">
            @csrf
            @method('POST')

            <!-- First Name -->
            <div>
                <label for="firstname" class="block text-sm font-semibold text-gray-300">First Name</label>
                <input type="text" id="firstname" name="firstname" value="{{ old('firstname', Auth::user()->firstname) }}" 
                       class="w-full p-3 mt-1 rounded-lg bg-gray-100 border border-gray-600 text-black focus:border-mustardOrange focus:ring-2 focus:ring-mustardOrange focus:outline-none transition"
                       style="text-transform: capitalize;">
                @error('firstname')
                    <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Last Name -->
            <div>
                <label for="lastname" class="block text-sm font-semibold text-gray-300">Last Name</label>
                <input type="text" id="lastname" name="lastname" value="{{ old('lastname', Auth::user()->lastname) }}" 
                       class="w-full p-3 mt-1 rounded-lg bg-gray-100 border border-gray-600 text-black focus:border-mustardOrange focus:ring-2 focus:ring-mustardOrange focus:outline-none transition"
                       style="text-transform: capitalize;">
                @error('lastname')
                    <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit Button -->
            <div class="flex justify-center mt-6">
                <button type="submit" class="bg-mustardOrange text-black font-bold py-2 px-6 rounded-lg hover:bg-yellow-500 transition">
                    Update Profile
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
