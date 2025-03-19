<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\Accounts;
use App\Models\Expense;
use App\Models\Project;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Pagination\LengthAwarePaginator;

class ExpensesController extends Controller
{
    public function ViewExpensesPage(Request $request)
{
    // Retrieve all active expenses for calculations and summaries
    $allExpenses = Expense::where('status', 'active')
        ->with(['releasedBy', 'project'])
        ->get()
        ->map(function ($expense) {
            try {
                $expense->amount = (float) Crypt::decryptString($expense->amount);
                $expense->date = \Carbon\Carbon::parse($expense->date);
            } catch (\Exception $e) {
                $expense->amount = 0;
            }
            return $expense;
        });

    // Start query for paginated expenses
    $query = Expense::where('status', 'active')->with(['releasedBy', 'project']);

    // Fetch paginated results first
    $transactions = $query->latest()->paginate(8)->appends($request->query());

    // Decrypt amounts and convert date
    $transactions->getCollection()->transform(function ($transaction) {
        try {
            $transaction->amount = (float) Crypt::decryptString($transaction->amount);
            $transaction->date = \Carbon\Carbon::parse($transaction->date);
        } catch (\Exception $e) {
            $transaction->amount = 0;
        }
        return $transaction;
    });

    // Apply search filter
    if ($request->has('search') && !empty($request->search)) {
        $search = strtolower($request->search);

        // Filter transactions after decryption
        $filteredTransactions = $transactions->getCollection()->filter(function ($expense) use ($search) {
            $expenseAmount = (string) $expense->amount;
            $remarks = strtolower($expense->remarks ?? '');
            $expenseDate = strtolower($expense->date->format('Y-m-d')); // 2025-03-13
            $expenseFullDate = strtolower($expense->date->format('F j, Y')); // March 13, 2025
            $releasedBy = strtolower(optional($expense->releasedBy)->firstname . ' ' . optional($expense->releasedBy)->lastname);
            $projectName = strtolower(optional($expense->project)->name ?? '');

            return str_contains($expenseAmount, $search) ||  // Searching by amount
                   str_contains($remarks, $search) ||
                   str_contains($expenseDate, $search) ||
                   str_contains($expenseFullDate, $search) ||
                   str_contains($releasedBy, $search) ||
                   str_contains($projectName, $search);
        });

        // Manually update the paginated results
        $transactions = new \Illuminate\Pagination\LengthAwarePaginator(
            $filteredTransactions->forPage($transactions->currentPage(), $transactions->perPage()),
            $filteredTransactions->count(),
            $transactions->perPage(),
            $transactions->currentPage(),
            ['path' => request()->url(), 'query' => request()->query()]
        );
    }

    return view('user.Expenses.Expenses', compact('transactions', 'allExpenses'));
}


    


    public function ViewAddExpensePage()
    {
        $accounts = Accounts::all();
        $projects = Project::where('status', 'ongoing')->get();

        return view('user.Expenses.AddExpense', compact('projects', 'accounts'));
    }

    public function storeExpense(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0', 
            'project_id' => 'required|exists:projects,id',
            'remarks' => 'nullable|string',
            'receipt' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $releasedById = Auth::id(); 
        $currentDate = Carbon::now()->toDateString();
        $filename = null;

        if ($request->hasFile('receipt')) {
            $file = $request->file('receipt');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('images'), $filename);
        }

        // Encrypt the amount before saving
        Expense::create([
            'amount' => Crypt::encryptString($request->amount),
            'released_by' => $releasedById, 
            'date' => $currentDate,
            'project_id' => $request->project_id,
            'remarks' => $request->remarks,
            'attachment' => $filename,
        ]);

        return redirect()->route('ViewExpensesPage')->with('success', 'Expense added successfully.');
    }

    public function ViewEditExpensePage(Request $request)
    {
        $expense = Expense::find($request->expense_id);
        $accounts = Accounts::all();
        $projects = Project::where('status', 'ongoing')->get();

        // Decrypt amount
        try {
            $expense->amount = (float) Crypt::decryptString($expense->amount);
        } catch (\Exception $e) {
            $expense->amount = 0;
        }

        return view('user.Expenses.EditExpense', compact('projects', 'accounts', 'expense'));
    }

    public function storeEditExpense(Request $request)
    {
        $validated = $request->validate([
            'expense_id' => 'required|exists:expenses,id',
            'amount' => 'required|numeric|min:0', 
            'project_id' => 'required|exists:projects,id',
            'remarks' => 'nullable|string',
            'attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120', 
        ]);

        $expense = Expense::findOrFail($validated['expense_id']);

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('images'), $filename);

            if ($expense->attachment) {
                $oldFile = public_path('images/' . $expense->attachment);
                if (file_exists($oldFile)) {
                    unlink($oldFile);
                }
            }

            $expense->attachment = $filename;
        }

        $expense->update([
            'amount' => Crypt::encryptString($request->amount), // Encrypt before saving
            'project_id' => $request->project_id,
            'remarks' => $request->remarks,
            'attachment' => $expense->attachment, 
        ]);

        return redirect()->route('ViewExpensesPage')->with('success', 'Expense updated successfully.');
    }

    public function updateExpenseStatusRemoved(Request $request)
    {
        $validated = $request->validate([
            'expense_id' => 'required|exists:expenses,id',
        ]);

        $expense = Expense::find($validated['expense_id']);
        $removedById = Auth::id(); 

        $expense->update([
            'status' => 'removed',
            'removed_by' => $removedById,
            'date_removed' => now(), 
        ]);

        return redirect()->route('ViewExpensesPage')->with('success', 'Expense removed successfully!');
    }
}
