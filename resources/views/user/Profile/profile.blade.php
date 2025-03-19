@extends('layouts.app')

@section('title', 'Profile - FusionBuild')

@section('content')
<div class="p-8 max-w-3xl mx-auto relative">
    <div class="bg-darkBlue text-white p-8 rounded-lg shadow-lg">

        <!-- Back to Dashboard Button -->
        <div class="flex justify-end mb-4">
            <a href="/Dashboard" class="bg-mustardOrange text-black font-bold py-2 px-4 rounded-lg hover:bg-yellow-500 transition">
                ← Dashboard
            </a>
        </div>

        <!-- Success Message -->
        @if (session('success'))
            <p class="text-green-400 text-center mb-4">{{ session('success') }}</p>
        @endif

        <!-- Profile Picture Section -->
        <div class="relative flex justify-center items-center mb-4">
            @if(Auth::user()->profile_picture)
                <img src="{{ asset('storage/' . Auth::user()->profile_picture) }}" 
                     alt="Profile Picture" 
                     class="w-24 h-24 rounded-full border-4 border-mustardOrange shadow-md">
            @else
                <div class="w-24 h-24 flex items-center justify-center rounded-full border-4 border-mustardOrange bg-gray-600 text-white text-2xl font-bold shadow-md">
                    {{ strtoupper(substr(Auth::user()->firstname, 0, 1)) }}
                </div>
            @endif
        </div>

        <!-- User Full Name -->
        <div class="text-center mb-2">
    <p class="text-3xl font-bold text-mustardOrange capitalize">
        {{ auth()->user()->firstname }} {{ auth()->user()->lastname }}
    </p>
</div>


        <!-- User Email -->
        <p class="text-center text-gray-300 mb-6 text-sm">
            {{ Auth::user()->email }}
        </p>

        <!-- Frame around Cards -->
        <div class="rounded-xl p-6 mt-6 bg-darkBlue">

            <!-- Cards Section -->
            <div class="grid grid-cols-1 gap-4">
                
                <!-- Update Information Card -->
                <a href="/Profile-update" class="block bg-gray-700 text-white p-4 rounded-lg shadow-lg hover:bg-gray-600 transition" onclick="return confirm('Are you sure you want to update your information?');">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-semibold">Update Information</h3>
                            <p class="text-sm text-gray-300">Modify your personal details.</p>
                        </div>
                        <span class="text-mustardOrange font-bold">→</span>
                    </div>
                </a>

                <!-- Update Email Card -->
                <a href="/Email-update" class="block bg-gray-700 text-white p-4 rounded-lg shadow-lg hover:bg-gray-600 transition" onclick="return confirm('Are you sure you want to update your email?');">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-semibold">Update Email</h3>
                            <p class="text-sm text-gray-300">Change your registered email.</p>
                        </div>
                        <span class="text-mustardOrange font-bold">→</span>
                    </div>
                </a>

                <!-- Change Password Card -->
                <a href="/Password-update-otp" class="block bg-gray-700 text-white p-4 rounded-lg shadow-lg hover:bg-gray-600 transition" onclick="return confirm('Are you sure you want to change your password?');">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-semibold">Change Password</h3>
                            <p class="text-sm text-gray-300">Update your password for security.</p>
                        </div>
                        <span class="text-mustardOrange font-bold">→</span>
                    </div>
                </a>
                
            </div>
        </div>

    </div>
</div>
@endsection

