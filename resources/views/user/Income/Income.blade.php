@extends('layouts.app')

@section('title', 'Income | FusionBuild')

@section('content')
<div class="bg-grayDarker p-8 rounded-lg">
    @if(session('success'))
        <div class="bg-green-500 text-white p-4 rounded-lg mb-6">
            {{ session('success') }}
        </div>
    @endif

    <h2 class="text-2xl font-bold text-primary mb-6 text-black">Income Overview</h2>

    <!-- Funds Summary -->
    <div class="bg-grayLight p-6 rounded-lg text-textLight flex justify-between items-center mb-8 shadow-md">
        <div>
            <h3 class="text-lg text-black">Total Income</h3>
            <p class="text-4xl font-bold text-primary text-black">
            ₱ {{ number_format($transaction->sum('amount'), 2) }}

            </p>
        </div>
        <a href="/income-add" class="bg-mustardOrange text-black px-5 py-3 rounded-lg font-semibold hover:bg-opacity-80 transition shadow-md">
            Add Income
        </a>
    </div>

    <!-- Filter & Search -->
<div class="flex flex-col md:flex-row md:items-center justify-between mb-6 space-y-4 md:space-y-0">
    <!-- Filter by Mode of Payment -->
    <div>
        <label for="statusFilter" class="text-black font-semibold">Filter by Mode of Payment:</label>
        <select id="statusFilter" class="border p-2 text-black rounded-lg">
    <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>All</option>
    <option value="cash" {{ request('status') == 'cash' ? 'selected' : '' }}>Cash</option>
    <option value="bank transfer" {{ request('status') == 'bank transfer' ? 'selected' : '' }}>Bank Transfer</option>
    <option value="gcash" {{ request('status') == 'gcash' ? 'selected' : '' }}>GCash</option>
    <option value="check" {{ request('status') == 'check' ? 'selected' : '' }}>Check</option>
</select>
    </div>

    <!-- Search Input -->
    <div class="flex items-center">
        <label for="searchInput" class="text-black font-semibold mr-2">Search:</label>
        <input type="text" id="searchInput" class="border p-2 rounded-lg text-black"
               placeholder="Search..." value="{{ request('search') }}">
        <button onclick="applyFilters()"
                class="bg-[#001f3f] text-white font-semibold py-2 px-4 ml-2 rounded-lg hover:bg-[#001a35] transition">
            Search
        </button>
    </div>
</div>


    <!-- Transactions Table -->
    <div class="bg-grayLight p-6 rounded-lg shadow-md">
        <h3 class="text-lg font-semibold text-primary mb-4 text-black">Transaction History</h3>
        <div class="overflow-x-auto border rounded-lg">
            <table class="w-full text-left border-collapse table-auto">
                <thead>
                    <tr class="bg-sidebar text-white bg-[#001f3f] rounded-t-lg">
                        <th class="py-3 px-1 whitespace-nowrap text-center">ID</th>
                        <th class="py-4 px-6 whitespace-nowrap text-center">AMOUNT</th>
                        <th class="py-4 px-6 whitespace-nowrap text-center">RECEIVED BY</th>
                        <th class="py-4 px-6 whitespace-nowrap text-center">DATE</th>
                        <th class="py-4 px-6 whitespace-nowrap text-center">MODE OF PAYMENT</th>
                        <th class="py-4 px-6 whitespace-nowrap text-center">PROJECT</th>
                        <th class="py-4 px-6 whitespace-nowrap text-center">REMARKS</th>
                        <th class="py-4 px-6 whitespace-nowrap text-center">ACTION</th>
                    </tr>
                </thead>
                <tbody id="transactionTable">
                    @forelse ($transactions as $transaction)
                        <tr class="border-b text-black transaction-row" data-status="{{ strtolower($transaction->mode_of_payment) }}">
                            <td class="py-3 px-4 text-center">{{ $transaction->id }}</td>
                            <td class="py-3 px-4 text-center">₱ {{ number_format($transaction->amount, 2) }}</td>
                            <td class="py-3 px-4 text-center">
                                {{ optional($transaction->inputBy)->firstname ?? 'N/A' }} 
                                {{ optional($transaction->inputBy)->lastname ?? '' }}
                            </td>
                            <td class="py-3 px-4 text-center">
                                {{ \Carbon\Carbon::parse($transaction->date)->format('M d, Y') }}
                            </td>
                            <td class="py-3 px-4 text-center">{{ ucfirst($transaction->mode_of_payment) }}</td>
                            <td class="py-3 px-4 text-center">{{ optional($transaction->project)->name ?? 'N/A' }}</td>
                            <td class="py-3 px-4 text-center">{{ $transaction->remarks ?? 'N/A' }}</td>
                            <td class="py-3 px-6 whitespace-nowrap text-center">
    <form action="{{ route('ViewEditIncomePage') }}" method="GET" class="inline">
        @csrf
        <input type="hidden" name="income_id" value="{{ $transaction->id }}">
        <button type="submit" class="w-24 px-4 py-2 text-sm font-semibold rounded-lg bg-[#001f3f] text-white hover:bg-[#001a35] transition">
            Edit
        </button>
    </form>
    <form action="{{ route('updateIncomeStatusRemoved') }}" method="POST" class="inline" 
    onsubmit="return confirm('Are you sure you want to remove this income record? This action cannot be undone.');">
    @csrf
    <input type="hidden" name="income_id" value="{{ $transaction->id }}">
    <button type="submit" class="w-24 px-4 py-2 text-sm font-semibold rounded-lg bg-red-500 text-white hover:bg-red-600 transition">
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

<script>
    document.addEventListener("DOMContentLoaded", function () {
    let statusFilter = document.getElementById("statusFilter");
    let searchInput = document.getElementById("searchInput");

    // Apply filters when status dropdown changes
    statusFilter.addEventListener("change", applyFilters);

    // Trigger search when Enter is pressed
    searchInput.addEventListener("keypress", function (event) {
        if (event.key === "Enter") {
            applyFilters();
        }
    });
});

function applyFilters() {
    let statusValue = document.getElementById("statusFilter").value;
    let searchValue = document.getElementById("searchInput").value.trim();
    let url = new URL(window.location.href);

    // Remove existing pagination parameter to reset to page 1
    url.searchParams.delete("page");

    // Apply status filter
    statusValue !== "all" ? url.searchParams.set("status", statusValue) : url.searchParams.delete("status");

    // Apply search filter
    searchValue !== "" ? url.searchParams.set("search", searchValue) : url.searchParams.delete("search");

    window.location.href = url.toString();
}
</script>
@endsection
