@extends('layouts.app')

@section('title', 'Update Email - FusionBuild')

@section('content')
<div class="p-8 max-w-2xl mx-auto">
    <div class="bg-darkBlue text-white p-6 rounded-lg shadow-lg">
        
        <!-- Back to Profile Button -->
        <div class="flex justify-end mb-6">
            <a href="/Email-update" class="bg-mustardOrange text-black font-bold py-2 px-4 rounded-lg hover:bg-yellow-500 transition">
                ← Back
            </a>
        </div>

        <h2 class="text-2xl font-bold text-center text-mustardOrange mb-6">Update Email</h2>

        <!-- Success Message -->
        @if (session('success'))
            <p class="text-green-400 text-center mb-4">{{ session('success') }}</p>
        @endif

        <!-- Error Message -->
        @if (session('error'))
            <p class="text-red-400 text-center mb-4">{{ session('error') }}</p>
        @endif
        @error('new_email')
                    <p class="text-red-400 text-center mb-4">{{ $message }}</p>
                @enderror

        <!-- Update Email Form -->
        <form action="{{ route('NewEmailUpdate') }}" method="POST" class="space-y-4">
            @csrf
            @method('POST')

            <!-- New Email Input -->
            <div>
                <label for="new_email" class="block text-sm font-semibold text-gray-300">Enter New Email</label>
                <input type="email" id="new_email" name="new_email" value="{{ old('new_email') }}" 
                       class="w-full p-3 mt-1 rounded-lg bg-gray-100 border border-gray-600 text-black focus:ring-2 focus:ring-mustardOrange focus:outline-none transition" required>

            </div>


            <!-- Submit Button -->
            <div class="flex justify-center mt-6">
                <button type="submit" class="bg-mustardOrange text-black font-bold py-2 px-6 rounded-lg hover:bg-yellow-500 transition">
                    Update Email
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
