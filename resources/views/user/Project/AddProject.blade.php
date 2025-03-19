@extends('layouts.app')

@section('title', 'Add Project | FusionBuild')

@section('content')
<div class="flex justify-center mt-16">
    <div class="bg-grayLight p-8 rounded-lg shadow-lg max-w-3xl w-full">
    <h2 class="text-2xl font-bold text-primary mb-6 text-black">Add New Project</h2>

    <!-- Form -->
    <form action="{{ route('storeProject') }}" method="POST" class="space-y-6">
        @csrf

        <!-- Project Name -->
        <div>
            <label class="block text-black font-semibold mb-2">Project Name</label>
            <input type="text" name="name" class="w-full p-3 border rounded-lg text-black" required>
        </div>

        <!-- Coordinator -->
        <div>
            <label class="block text-black font-semibold mb-2">Coordinator</label>
            <input type="text" name="coordinator" class="w-full p-3 border rounded-lg text-black" required>
        </div>

        <!-- Remarks -->
        <div>
            <label class="block text-black font-semibold mb-2">Remarks</label>
            <textarea name="remarks" rows="3" class="w-full p-3 border rounded-lg text-black"></textarea>
        </div>

         <!-- Buttons -->
         <div class="flex justify-end space-x-2">
            <a href="{{ route('ViewProjectPage') }}" class="bg-gray-500 text-white px-6 py-3 rounded-lg font-semibold hover:bg-gray-600 transition shadow-md">
                Cancel
            </a>
            <button type="submit" class="bg-mustardOrange text-black px-6 py-3 rounded-lg font-semibold hover:bg-opacity-80 transition shadow-md" 
    onclick="return confirm('Are you sure you want to add this project?');">
    Add Project
</button>

        </div>
    </form>
</div>
</div>
@endsection
