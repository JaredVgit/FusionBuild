@extends('layouts.app')

@section('title', 'Expenses | FusionBuild')

@section('content')
<div class="bg-grayDarker p-8 rounded-lg">
    @if(session('success'))
        <div class="bg-green-500 text-white p-4 rounded-lg mb-6">
            {{ session('success') }}
        </div>
    @endif

    <h2 class="text-2xl font-bold text-primary mb-6 text-black">Expenses Overview</h2>

    <!-- Funds Summary -->
    <div class="bg-grayLight p-6 rounded-lg text-textLight flex justify-between items-center mb-8 shadow-md">
        <div>
            <h3 class="text-lg text-black">Total Expenses</h3>
            <p class="text-4xl font-bold text-primary text-black">
                ₱ {{ number_format($allExpenses->sum('amount'), 2) }}
            </p>
        </div>
        <a href="/expense-add" class="bg-mustardOrange text-black px-5 py-3 rounded-lg font-semibold hover:bg-opacity-80 transition shadow-md">
            Add Expense
        </a>
    </div>

    <!-- Search Form -->
<form method="GET" action="{{ route('ViewExpensesPage') }}" class="flex justify-end mb-6">
    <div class="flex items-center space-x-2">
        <label for="searchInput" class="text-black font-semibold">Search:</label>
        <input type="text" id="searchInput" name="search" class="border p-2 rounded-lg text-black"
               placeholder="Search..." value="{{ request('search') }}">
        <button type="submit"
                class="bg-[#001f3f] text-white font-semibold py-2 px-4 rounded-lg hover:bg-[#001a35] transition">
            Search
        </button>
    </div>
</form>


    <!-- Transactions Table -->
    <div class="bg-grayLight p-6 rounded-lg shadow-md">
        <h3 class="text-lg font-semibold text-primary mb-4 text-black">Transaction History</h3>
        <div class="overflow-x-auto border rounded-lg">
            <table class="w-full text-left border-collapse table-auto">
                <thead>
                    <tr class="bg-sidebar text-white bg-[#001f3f] rounded-t-lg">
                        <th class="py-3 px-1 whitespace-nowrap text-center">ID</th>
                        <th class="py-4 px-6 whitespace-nowrap text-center">AMOUNT</th>
                        <th class="py-4 px-6 whitespace-nowrap text-center">PROJECT</th>
                        <th class="py-4 px-6 whitespace-nowrap text-center">RELEASED BY</th>
                        <th class="py-4 px-6 whitespace-nowrap text-center">DATE</th>
                        <th class="py-4 px-6 whitespace-nowrap text-center">ATTACHMENT</th>
                        <th class="py-4 px-6 whitespace-nowrap text-center">REMARKS</th>
                        <th class="py-4 px-6 whitespace-nowrap text-center">ACTION</th>
                    </tr>
                </thead>
                <tbody id="transactionTable">
                    @forelse ($transactions as $expense)
                        <tr class="border-b text-black transaction-row">
                            <td class="py-3 px-4 text-center">{{ $expense->id}}</td>
                            <td class="py-3 px-4 text-center">₱ {{ number_format($expense->amount, 2) }}</td>
                            <td class="py-3 px-4 text-center">{{ optional($expense->project)->name ?? 'N/A' }}</td>
                            <td class="py-3 px-4 text-center">
                                {{ optional($expense->releasedBy)->firstname ?? 'N/A' }} 
                                {{ optional($expense->releasedBy)->lastname ?? '' }}
                            </td>
                            <td class="py-3 px-4 text-center">
                                {{ \Carbon\Carbon::parse($expense->date)->format('M d, Y') }}
                            </td>
                            <td class="py-3 px-4 text-center">
    @if($expense->attachment)
        <button onclick="openAttachmentModal('{{ asset('images/' . $expense->attachment) }}')" class="text-blue-500 hover:underline">
            View
        </button>
    @else
        <span class="text-gray-500">No File</span>
    @endif
</td>

                            <td class="py-3 px-4 text-center">{{ $expense->remarks ?? 'N/A' }}</td>
                            <td class="py-3 px-6 whitespace-nowrap text-center">
        <form action="{{ route('ViewEditExpensePage') }}" method="GET" class="inline">
        @csrf
        <input type="hidden" name="expense_id" value="{{ $expense->id }}">
        <button type="submit" class="bg-[#001f3f] text-white px-5 py-2 text-sm font-semibold rounded-lg hover:bg-[#001a35] transition w-[90px] text-center">
            Edit
        </button>
    </form>
    <form action="{{ route('updateExpenseStatusRemoved') }}" method="POST" class="inline" 
    onsubmit="return confirm('Are you sure you want to remove this expense record? This action cannot be undone.');">
    @csrf
    <input type="hidden" name="expense_id" value="{{ $expense->id }}">
    <button type="submit" class="bg-red-500 text-white px-5 py-2 text-sm font-semibold rounded-lg hover:bg-red-600 transition w-[90px] text-center">
        Remove
    </button>
</form>

</td>

                        </tr>
                    @empty
                        <tr class="bg-gray-600 text-white text-center">
                            <td class="py-3 px-4 border border-gray-500" colspan="8">No transactions found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
    {{ $transactions->links() }}
</div>
    </div>
</div>

<!-- Attachment Modal -->
<div id="attachmentModal" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center hidden z-50">
    <div class="bg-grayLight py-6 px-4 md:px-4 rounded-lg shadow-lg w-11/12 max-w-2xl relative">
        <button onclick="closeAttachmentModal()" class="absolute top-2 right-3 text-gray-700 hover:text-red-500 text-2xl font-bold">&times;</button>
        <h2 class="text-xl font-bold text-primary mb-2 text-black">Attachment Preview</h2>
        <div class="max-h-[60vh] overflow-auto">
            <img id="attachmentImage" src="" class="w-full h-auto rounded-lg" alt="Attachment">
        </div>
    </div>
</div>



<script>
    function filterTransactions() {
        let searchQuery = document.getElementById("searchInput").value.toLowerCase();
        let rows = document.querySelectorAll(".transaction-row");

        rows.forEach(row => {
            let rowData = row.textContent.toLowerCase();
            let matchesSearch = rowData.includes(searchQuery);
            row.style.display = matchesSearch ? "" : "none";
        });
    }

    function openAttachmentModal(imageUrl) {
        const modal = document.getElementById("attachmentModal");
        document.getElementById("attachmentImage").src = imageUrl;
        modal.classList.remove("hidden");
        modal.classList.add("flex");
    }

    function closeAttachmentModal() {
        const modal = document.getElementById("attachmentModal");
        modal.classList.add("hidden");
        modal.classList.remove("flex");
    }

    // Close modal when clicking outside the image
    document.getElementById("attachmentModal").addEventListener("click", function(event) {
        if (event.target === this) closeAttachmentModal();
    });

</script>
@endsection
