<?php

namespace App\Http\Controllers;
use App\Models\Expense;
use App\Models\Income;
use App\Models\Project;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class DashboardController extends Controller
{
    public function Dashboard()
{
    $expenses = Expense::where('status', 'active')->with(['releasedBy', 'project'])->latest()->get();
    $incomes = Income::where('status', 'active')->with(['inputBy', 'project'])->latest()->get();

    // Ensure amounts are properly decrypted (if encrypted) and cast to float
    $expenses->transform(function ($expense) {
        $expense->amount = (float) Crypt::decryptString($expense->amount); // Decrypt amount
        return $expense;
    });

    $incomes->transform(function ($income) {
        $income->amount = (float) Crypt::decryptString($income->amount); // Decrypt amount
        return $income;
    });

    // Calculate balance
    $balance = $incomes->sum('amount') - $expenses->sum('amount');

    // Merge both transactions and sort by the latest
    $recentTransactions = collect()
        ->merge($incomes)
        ->merge($expenses)
        ->sortByDesc('created_at')
        ->take(5);

    $months = collect(range(1, 12))->map(fn($month) => date("F", mktime(0, 0, 0, $month, 1)));

    // Monthly totals
    $monthlyIncome = $incomes->groupBy(fn($income) => $income->created_at->format('F'))
        ->map(fn($group) => $group->sum('amount'))
        ->toArray();

    $monthlyExpenses = $expenses->groupBy(fn($expense) => $expense->created_at->format('F'))
        ->map(fn($group) => $group->sum('amount'))
        ->toArray();

    // Fetch new projects per month
    $monthlyNewProjects = Project::whereYear('created_at', now()->year)
        ->get()
        ->groupBy(fn($project) => $project->created_at->format('F'))
        ->map(fn($group) => $group->count())
        ->toArray();

    return view('user.Dashboard', compact(
        'expenses', 'incomes', 'balance', 'recentTransactions',
        'months', 'monthlyIncome', 'monthlyExpenses', 'monthlyNewProjects'
    ));
}


public function getChartData(Request $request)
{
    $filter = $request->query('filter', 'monthly');

    if ($filter === 'weekly') { 
        // Fetch the current week and the past 4 weeks
        $labels = collect();
        $weekKeys = collect();

        for ($i = 4; $i >= 0; $i--) {
            $weekStart = now()->subWeeks($i)->startOfWeek();
            $weekEnd = now()->subWeeks($i)->endOfWeek();
            $weekKey = $weekStart->format('Y-W'); // Store year-week format for database matching
            
            $weekKeys->push($weekKey);
            $labels->push($weekStart->format('M d') . " - " . $weekEnd->format('M d')); // Format: "Feb 10 - Feb 16"
        }

        $period = now()->subWeeks(4)->startOfWeek();
        $format = 'Y-W'; // Use Year-Week format for grouping

    } elseif ($filter === 'monthly') { 
        // Fetch the current month and the past 4 months
        $labels = collect();
        $monthKeys = collect();
        
        for ($i = 4; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $monthKey = $month->format('Y-m'); // Store in "2024-03" format
            $monthKeys->push($monthKey);
            $labels->push($month->format('F Y')); // Example: "March 2024"
        }

        $period = now()->subMonths(4)->startOfMonth();
        $format = 'Y-m'; // Use Year-Month format for grouping

    } elseif ($filter === 'yearly') {
        // Fetch the current year and the past 4 years
        $startYear = now()->year - 4; // Last 5 years
        $labels = collect(range($startYear, now()->year))->map(fn($y) => (string) $y);
        $period = Carbon::create($startYear, 1, 1);
        $format = 'Y';

    } else {
        return response()->json(['error' => 'Invalid filter'], 400);
    }

    // Fetch Income, Expenses, and Projects based on the selected period
    $incomeData = Income::where('created_at', '>=', $period)
        ->where('status', '!=', 'removed')
        ->get()
        ->map(function ($income) {
            $income->amount = is_numeric($income->amount) ? (float) $income->amount : (float) Crypt::decryptString($income->amount);
            return $income;
        })
        ->groupBy(fn($d) => $d->created_at->format($format))
        ->map->sum('amount')
        ->toArray();

    $expenseData = Expense::where('created_at', '>=', $period)
        ->where('status', '!=', 'removed')
        ->get()
        ->map(function ($expense) {
            $expense->amount = is_numeric($expense->amount) ? (float) $expense->amount : (float) Crypt::decryptString($expense->amount);
            return $expense;
        })
        ->groupBy(fn($d) => $d->created_at->format($format))
        ->map->sum('amount')
        ->toArray();

    $projectData = Project::where('created_at', '>=', $period)
        ->where('status', '!=', 'removed')
        ->get()
        ->groupBy(fn($d) => $d->created_at->format($format))
        ->map->count()
        ->toArray();

    return response()->json([
        'labels' => $labels->toArray(),
        'income' => $labels->map(fn($l, $index) => $incomeData[$weekKeys[$index] ?? $monthKeys[$index] ?? $l] ?? 0)->toArray(),
        'expenses' => $labels->map(fn($l, $index) => $expenseData[$weekKeys[$index] ?? $monthKeys[$index] ?? $l] ?? 0)->toArray(),
        'projects' => $labels->map(fn($l, $index) => $projectData[$weekKeys[$index] ?? $monthKeys[$index] ?? $l] ?? 0)->toArray(),
    ]);
}


public function ViewTransactions(Request $request)
{
    // Fetch transactions
    $expenses = Expense::where('status', 'active')->with(['releasedBy', 'project'])->latest()->get();
    $incomes = Income::where('status', 'active')->with(['inputBy', 'project'])->latest()->get();

    // Decrypt expense amounts if encrypted
    $expenses->transform(function ($expense) {
        $expense->amount = is_numeric($expense->amount) ? (float) $expense->amount : (float) Crypt::decryptString($expense->amount);
        return $expense;
    });

    // Decrypt income amounts if encrypted
    $incomes->transform(function ($income) {
        $income->amount = is_numeric($income->amount) ? (float) $income->amount : (float) Crypt::decryptString($income->amount);
        return $income;
    });

    // Merge and sort transactions in descending order by date
    $transactions = collect()
        ->merge($incomes)
        ->merge($expenses)
        ->sortByDesc('created_at')
        ->values(); // Reset keys after sorting

    // Apply search filter
    if ($request->has('search') && !empty($request->search)) {
        $search = strtolower($request->search);
        $transactions = $transactions->filter(function ($transaction) use ($search) {
            $projectName = strtolower($transaction->project->name ?? '');
            $transactionType = $transaction instanceof Income ? 'income' : 'expense';
            $transactionAmount = (string) $transaction->amount;
            $transactionDate = strtolower($transaction->created_at->format('Y-m-d')); // 2025-03-13
            $transactionMonthFull = strtolower($transaction->created_at->format('F')); // March
            $transactionMonthShort = strtolower($transaction->created_at->format('M')); // Mar
            $transactionDay = strtolower($transaction->created_at->format('j')); // 13
            $transactionYear = strtolower($transaction->created_at->format('Y')); // 2025
            $transactionFullDate = strtolower($transaction->created_at->format('F j, Y')); // March 13, 2025

            return str_contains($projectName, $search) ||
                   str_contains($transactionType, $search) ||
                   str_contains($transactionAmount, $search) ||
                   str_contains($transactionDate, $search) ||
                   str_contains($transactionMonthFull, $search) ||
                   str_contains($transactionMonthShort, $search) ||
                   str_contains($transactionDay, $search) ||
                   str_contains($transactionYear, $search) ||
                   str_contains($transactionFullDate, $search);
        })->values(); // Reset keys after filtering
    }

    // Apply transaction type filter (income/expense)
    if ($request->has('filter') && in_array($request->filter, ['income', 'expense'])) {
        $transactions = $transactions->filter(function ($transaction) use ($request) {
            return ($request->filter === 'income' && $transaction instanceof Income) ||
                   ($request->filter === 'expense' && $transaction instanceof Expense);
        })->values(); // Reset keys after filtering
    }

    // Paginate manually since collections don't support automatic pagination
    $perPage = 8;
    $currentPage = request()->input('page', 1);
    $paginatedTransactions = new \Illuminate\Pagination\LengthAwarePaginator(
        $transactions->forPage($currentPage, $perPage),
        $transactions->count(),
        $perPage,
        $currentPage,
        ['path' => request()->url(), 'query' => request()->query()] // Preserve query parameters
    );

    return view('user.Transactions', compact('paginatedTransactions'));
}


}
