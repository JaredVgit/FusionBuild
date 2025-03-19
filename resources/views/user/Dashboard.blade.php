@extends('layouts.app')

@section('title', 'Dashboard - FusionBuild')

@section('content')
<div class="bg-grayDarker p-8 rounded-lg">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div class="bg-white text-black p-6 rounded-lg shadow-lg">
            <h2 class="text-xl font-semibold text-darkBlue">Total Income</h2>
            <p class="text-3xl font-bold">₱ {{ number_format($incomes->sum('amount'), 2) }}</p>
        </div>
        <div class="bg-white text-black p-6 rounded-lg shadow-lg">
            <h2 class="text-xl font-semibold text-darkBlue">Total Expenses</h2>
            <p class="text-3xl font-bold">₱ {{ number_format($expenses->sum('amount'), 2) }}</p>
        </div>
        <div class="bg-white text-black p-6 rounded-lg shadow-lg">
            <h2 class="text-xl font-semibold text-darkBlue">Balance</h2>
            <p class="text-3xl font-bold">₱ {{ number_format($balance, 2) }}</p>
        </div>
    </div>

     <!-- Recent Transactions -->
<div class="mt-8 bg-white text-black p-6 rounded-lg shadow-lg">
    <div class="flex justify-between items-center">
        <h2 class="text-xl font-semibold text-darkBlue">Recent Transactions</h2>
        <a href="{{ route('ViewTransactions') }}" class="bg-[#001f3f] text-white px-4 py-2 rounded-lg hover:bg-[#001a35] transition">View All Transactions</a>
    </div>
    
    <div class="pl-4 pr-4">
        <ul class="space-y-2 mt-4 divide-y divide-gray-200">
            @forelse($recentTransactions as $transaction)
                <li class="flex justify-between items-center py-2">
                    <div>
                        <span class="text-gray-700 font-medium">
                            {{ $transaction instanceof App\Models\Income ? 'Income from' : 'Expense for' }}
                            <span class="font-semibold">{{ $transaction->project->name ?? 'No Project' }}</span>
                        </span>
                        <p class="text-sm text-gray-500">{{ $transaction->created_at->format('F j, Y - g:i A') }}</p>
                    </div>
                    <span class="{{ $transaction instanceof App\Models\Income ? 'text-green-600' : 'text-red-600' }} font-semibold text-lg">
                        ₱{{ number_format($transaction->amount, 2) }}
                    </span>
                </li>
            @empty
                <li class="bg-gray-600 text-white text-center py-3">
                    No transactions found
                </li>
            @endforelse
        </ul>
    </div>
</div>


   <!-- Monthly Overview -->
<div class="mt-4 bg-white text-black p-6 rounded-lg shadow-lg">
<h2 class="text-xl font-semibold text-darkBlue text-center">Overview</h2>
    <!-- Filter Dropdown -->
    <div class="flex justify-end mb-4">
        <div>
            <label class="text-black font-semibold mr-2">Filter by:</label>
            <select id="chartFilter" class="border p-2 rounded-lg text-black">
                <option value="weekly">Week</option>
                <option value="monthly" selected>Month</option>
                <option value="yearly">Year</option>
            </select>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
        <!-- Income & Expenses Chart -->
        <div class="bg-white text-black p-6 rounded-lg shadow-lg">
            <h2 class="text-lg font-semibold text-darkBlue text-center">Income & Expenses</h2>
            <div class="w-full">
                <canvas id="incomeExpenseChart" class="max-w-full"></canvas>
            </div>
        </div>

        <!-- New Projects Chart -->
        <div class="bg-white text-black p-6 rounded-lg shadow-lg">
            <h2 class="text-lg font-semibold text-darkBlue text-center">New Projects</h2>
            <div class="w-full">
                <canvas id="newProjectsChart" class="max-w-full"></canvas>
            </div>
        </div>
    </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        let chartFilter = document.getElementById("chartFilter");

        let incomeExpenseChart, newProjectsChart;
        let ctx1 = document.getElementById("incomeExpenseChart").getContext("2d");
        let ctx2 = document.getElementById("newProjectsChart").getContext("2d");

        function fetchChartData(filter) {
            fetch(`/dashboard/chart-data?filter=${filter}`)
                .then(response => response.json())
                .then(data => {
                    updateCharts(data);
                })
                .catch(error => console.error("Error fetching chart data:", error));
        }

        function updateCharts(data) {
            if (incomeExpenseChart) incomeExpenseChart.destroy();
            if (newProjectsChart) newProjectsChart.destroy();

            incomeExpenseChart = new Chart(ctx1, {
                type: "bar",
                data: {
                    labels: data.labels,
                    datasets: [
                        {
                            label: "Income",
                            data: data.income,
                            backgroundColor: "rgba(54, 162, 235, 0.6)",
                            borderColor: "rgba(54, 162, 235, 1)",
                            borderWidth: 1
                        },
                        {
                            label: "Expenses",
                            data: data.expenses,
                            backgroundColor: "rgba(255, 99, 132, 0.6)",
                            borderColor: "rgba(255, 99, 132, 1)",
                            borderWidth: 1
                        }
                    ]
                },
                options: { responsive: true, scales: { y: { beginAtZero: true } } }
            });

            newProjectsChart = new Chart(ctx2, {
                type: "line",
                data: {
                    labels: data.labels,
                    datasets: [
                        {
                            label: "New Projects",
                            data: data.projects,
                            backgroundColor: "rgba(75, 192, 192, 0.6)",
                            borderColor: "rgba(75, 192, 192, 1)",
                            borderWidth: 2,
                            fill: true,
                            tension: 0.4
                        }
                    ]
                },
                options: { responsive: true, scales: { y: { beginAtZero: true } } }
            });
        }

        chartFilter.addEventListener("change", function () {
            fetchChartData(this.value);
        });

        fetchChartData("monthly"); // Load default monthly data
    });
</script>
@endsection
