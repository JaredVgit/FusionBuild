@extends('layouts.app')

@section('title', 'All Transactions - FusionBuild')

@section('content')
<div class="bg-grayDarker p-8 rounded-lg">
    <div class="bg-white text-black p-6 rounded-lg shadow-lg">

        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-semibold text-darkBlue">All Transactions</h2>
            <a href="{{ route('Dashboard') }}" 
               class="bg-mustardOrange text-black font-bold py-2 px-4 rounded-lg hover:bg-yellow-500 transition">
                ← Dashboard
            </a>
        </div>

        <div class="px-4">
            <!-- Search & Filter -->
            <div class="flex justify-between items-center mb-4">
                <!-- Filter Dropdown -->
                <div>
                    <label for="filterSelect" class="text-black font-semibold">Filter:</label>
                    <select id="filterSelect" class="border p-2 rounded-lg text-black">
                        <option value="all" {{ request('filter') == 'all' ? 'selected' : '' }}>All</option>
                        <option value="income" {{ request('filter') == 'income' ? 'selected' : '' }}>Income</option>
                        <option value="expense" {{ request('filter') == 'expense' ? 'selected' : '' }}>Expense</option>
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

            <!-- Transactions List with Export & Print Options -->
            <div class="mt-2 bg-white text-black p-6 rounded-lg shadow-lg"> 
                <div class="px-4">
                    @include('partials.transactions-list', ['transactions' => $paginatedTransactions])
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        let filterSelect = document.getElementById("filterSelect");
        let searchInput = document.getElementById("searchInput");

        // Apply filter on dropdown change
        filterSelect.addEventListener("change", applyFilters);

        // Trigger search on Enter key
        searchInput.addEventListener("keypress", function (event) {
            if (event.key === "Enter") {
                event.preventDefault(); // Prevent form submission (if inside a form)
                applyFilters();
            }
        });
    });

    function applyFilters() {
        let filterValue = document.getElementById("filterSelect").value;
        let searchValue = document.getElementById("searchInput").value.trim();
        let url = new URL(window.location.href);

        // Remove pagination parameter to reset to first page
        url.searchParams.delete("page");

        // Apply filter
        filterValue !== "all" ? url.searchParams.set("filter", filterValue) : url.searchParams.delete("filter");

        // Apply search
        searchValue !== "" ? url.searchParams.set("search", searchValue) : url.searchParams.delete("search");

        // Reload the page with the new parameters
        window.location.href = url.toString();
    }
</script>



@endsection
