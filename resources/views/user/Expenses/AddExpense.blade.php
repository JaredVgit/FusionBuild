@extends('layouts.app')

@section('title', 'Add Expense | FusionBuild')

@section('content')
<div class="flex justify-center items-center min-h-screen">
    <div class="bg-grayLight p-6 md:p-8 rounded-lg shadow-lg w-full sm:w-3/4 lg:w-1/2">
        <h2 class="text-2xl font-bold text-primary mb-6 text-black">Add Expense</h2>

        @if ($errors->any())
            <div class="bg-red-500 text-white p-3 rounded-lg mb-4">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>- {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('storeExpense') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div class="space-y-2">
                <label for="amount" class="block text-black font-semibold">Amount (₱)</label>
                <input type="text" name="amount" id="amount" class="w-full p-2 border rounded-lg text-black"
                    required placeholder="Enter amount" value="{{ old('amount') }}"
                    oninput="validateAmount(this)">
            </div>

            <div class="space-y-2">
                <label for="project_id" class="block text-black font-semibold">Project</label>
                <select name="project_id" id="project_id" class="w-full p-2 border rounded-lg text-black" required>
                    <option value="" disabled selected>Select Project</option>
                    @foreach($projects as $project)
                        <option value="{{ $project->id }}" {{ old('project_id') == $project->id ? 'selected' : '' }}>
                            {{ $project->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="space-y-2">
                <label for="remarks" class="block text-black font-semibold">Remarks</label>
                <textarea name="remarks" id="remarks" class="w-full p-2 border rounded-lg text-black" placeholder="Enter remarks">{{ old('remarks') }}</textarea>
            </div>

            {{-- 🔹 File Attachment Section --}}
            <div class="space-y-2">
                <label for="receipt" class="block text-black font-semibold">Upload Receipt (PDF, JPG, PNG)</label>
                <input type="file" name="receipt" id="receipt" class="w-full p-2 border rounded-lg text-black"
                    accept=".pdf, .jpg, .jpeg, .png" onchange="validateFileSize(this)">
                <p class="text-sm text-gray-600">Max file size: 5MB</p>
            </div>

            <div class="flex justify-end space-x-2">
                <a href="expenses" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition">Cancel</a>
                <button type="submit" class="bg-mustardOrange text-black px-4 py-2 rounded-lg font-semibold hover:bg-opacity-80 transition" onclick="return confirm('Are you sure you want to add this expense record?');">
                    Add Expense
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function validateAmount(input) {
    input.value = input.value
        .replace(/[^0-9.]/g, '') // Allow only numbers and a single '.'
        .replace(/(\..*)\./g, '$1') // Prevent multiple dots
        .replace(/^0+(\d)/, '$1'); // Remove leading zeros

    let parts = input.value.split('.');
    if (parts[1] && parts[1].length > 2) {
        input.value = parts[0] + '.' + parts[1].slice(0, 2);
    }
}

function validateFileSize(input) {
    if (input.files[0] && input.files[0].size > 5 * 1024 * 1024) {
        alert("File size must be 5MB or less.");
        input.value = ''; // Clear the file input
    }
}
</script>

@endsection
