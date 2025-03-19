@extends('layouts.app')

@section('title', 'Edit Project | FusionBuild')

@section('content')
<div class="flex justify-center mt-16">
    <div class="bg-grayLight p-8 rounded-lg shadow-lg max-w-3xl w-full">
        <h2 class="text-2xl font-bold text-primary mb-6 text-black">Edit Project</h2>

        <!-- Display Validation Errors -->
        @if ($errors->any())
            <div class="bg-red-500 text-white p-4 rounded-lg mb-6">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Form -->
        <form action="{{ route('storeEditProject', $projects->id) }}" method="POST" class="space-y-6">
            @csrf
            @method('POST')

            <input type="hidden" name="project_id" value="{{ $projects->id }}">

            <!-- Project Name -->
            <div>
                <label class="block text-black font-semibold mb-2">Project Name</label>
                <input type="text" name="name" value="{{ old('name', $projects->name) }}" class="w-full p-3 border rounded-lg text-black" required>
            </div>

            <!-- Coordinator -->
            <div>
                <label class="block text-black font-semibold mb-2">Coordinator</label>
                <input type="text" name="coordinator" value="{{ old('coordinator', $projects->coordinator) }}" class="w-full p-3 border rounded-lg text-black" required>
            </div>

            <!-- Remarks -->
            <div>
                <label class="block text-black font-semibold mb-2">Remarks</label>
                <textarea name="remarks" rows="3" class="w-full p-3 border rounded-lg text-black">{{ old('remarks', $projects->remarks) }}</textarea>
            </div>

            <!-- Buttons -->
         <div class="flex justify-end space-x-2">
            <a href="{{ route('ViewProjectPage') }}" class="bg-gray-500 text-white px-6 py-3 rounded-lg font-semibold hover:bg-gray-600 transition shadow-md">
                Cancel
            </a>
            <button type="submit" class="bg-mustardOrange text-black px-6 py-3 rounded-lg font-semibold hover:bg-opacity-80 transition shadow-md" 
    onclick="return confirm('Are you sure you want to update this project?');">
    Update Project
</button>

            </div>
        </form>
    </div>
</div>
@endsection
