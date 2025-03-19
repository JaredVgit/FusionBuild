@extends('layouts.app')

@section('title', 'Edit Expense | FusionBuild')

@section('content')
<div class="flex justify-center items-center min-h-screen">
    <div class="bg-grayLight p-6 md:p-8 rounded-lg shadow-lg w-full sm:w-3/4 lg:w-1/2">
        <h2 class="text-2xl font-bold text-primary mb-6 text-black">Edit Expense</h2>

        @if ($errors->any())
            <div class="bg-red-500 text-white p-3 rounded-lg mb-4">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>- {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('storeEditExpense', $expense->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            @method('POST')

            <input type="hidden" name="expense_id" value="{{ $expense->id }}">

            <div class="space-y-2">
                <label for="amount" class="block text-black font-semibold">Amount (₱)</label>
                <input type="text" name="amount" id="amount" class="w-full p-2 border rounded-lg text-black"
                    required placeholder="Enter amount" value="{{ old('amount', $expense->amount) }}"
                    oninput="validateAmount(this)">
            </div>

            <div class="space-y-2">
                <label for="project_id" class="block text-black font-semibold">Project</label>
                <select name="project_id" id="project_id" class="w-full p-2 border rounded-lg text-black" required>
                    <option value="" disabled>Select Project</option>
                    @foreach($projects as $project)
                        <option value="{{ $project->id }}" {{ old('project_id', $expense->project_id) == $project->id ? 'selected' : '' }}>
                            {{ $project->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="space-y-2">
                <label for="remarks" class="block text-black font-semibold">Remarks</label>
                <textarea name="remarks" id="remarks" class="w-full p-2 border rounded-lg text-black"
                    placeholder="Enter remarks">{{ old('remarks', $expense->remarks) }}</textarea>
            </div>

            {{-- 🔹 File Attachment Section with Modal --}}
            <div class="space-y-2"> 
                <label for="attachment" class="block text-black font-semibold">Upload Attachment (PDF, JPG, PNG)</label>
                <input type="file" name="attachment" id="attachment" class="w-full p-2 border rounded-lg text-black"
                    accept=".pdf, .jpg, .jpeg, .png" onchange="validateFileSize(this)">
                <p class="text-sm text-gray-600">Max file size: 5MB</p>

                @if($expense->attachment)
                    <p class="text-sm text-gray-700">Current File: 
                        <button type="button" onclick="openAttachmentModal('{{ asset('images/' . $expense->attachment) }}')" class="text-blue-500 underline">
                            View Attachment
                        </button>
                    </p>
                    <input type="hidden" name="existing_attachment" value="{{ $expense->attachment }}">
                @endif
            </div>

            <div class="flex justify-end space-x-2">
                <a href="{{ route('ViewExpensesPage') }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition">Cancel</a>
                <button type="submit" class="bg-mustardOrange text-black px-4 py-2 rounded-lg font-semibold hover:bg-opacity-80 transition" 
    onclick="return confirm('Are you sure you want to update this expense record?');">
    Update Expense
</button>

            </div>
        </form>
    </div>
</div>
{{-- 🔹 Attachment Modal --}}
<div id="attachmentModal" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center hidden z-50">
    <div class="bg-grayLight py-6 px-4 md:px-4 rounded-lg shadow-lg w-11/12 max-w-2xl relative">
        <button onclick="closeAttachmentModal()" class="absolute top-2 right-3 text-gray-700 hover:text-red-500 text-2xl font-bold">&times;</button>
        <h2 class="text-xl font-bold text-primary mb-2 text-black">Attachment Preview</h2>

        <!-- Scrollable Attachment Viewer -->
        <div class="max-h-[60vh] overflow-auto">
            <iframe id="attachmentViewer" class="w-full h-72 hidden"></iframe>
            <img id="attachmentImage" class="w-full h-auto hidden">
        </div>
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

function openAttachmentModal(fileUrl) {
    const modal = document.getElementById("attachmentModal");
    const modalContent = modal.querySelector("div");
    const attachmentImage = document.getElementById("attachmentImage");
    const attachmentViewer = document.getElementById("attachmentViewer");

    // Check file extension
    if (fileUrl.endsWith(".pdf")) {
        attachmentViewer.src = fileUrl;
        attachmentViewer.classList.remove("hidden");
        attachmentImage.classList.add("hidden");
    } else {
        attachmentImage.src = fileUrl;
        attachmentImage.classList.remove("hidden");
        attachmentViewer.classList.add("hidden");
    }

    modal.classList.remove("hidden");

    // Animate popup
    setTimeout(() => {
        modalContent.classList.remove("scale-95", "opacity-0");
        modalContent.classList.add("scale-100", "opacity-100");
    }, 10);
}

function closeAttachmentModal() {
    const modal = document.getElementById("attachmentModal");
    const modalContent = modal.querySelector("div");

    // Animate closing
    modalContent.classList.remove("scale-100", "opacity-100");
    modalContent.classList.add("scale-95", "opacity-0");

    setTimeout(() => {
        modal.classList.add("hidden");
    }, 200);
}

// Close modal when clicking outside the modal box
document.getElementById("attachmentModal").addEventListener("click", function(event) {
    if (event.target === this) closeAttachmentModal();
});
</script>

@endsection
