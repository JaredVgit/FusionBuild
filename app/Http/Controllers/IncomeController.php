<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\Income;
use App\Models\Accounts;
use App\Models\Project;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Pagination\LengthAwarePaginator;

class IncomeController extends Controller
{
    public function ViewIncomePage(Request $request)
{
    // Retrieve all active transactions (for total calculations, summaries, etc.)
    $transaction = Income::where('status', 'active')
        ->with(['inputBy', 'project'])
        ->get()
        ->map(function ($transaction) {
            $transaction->amount = (float) Crypt::decryptString($transaction->amount);
            $transaction->date = \Carbon\Carbon::parse($transaction->date); // Convert to Carbon
            return $transaction;
        });

    // Start query for paginated transactions
    $query = Income::where('status', 'active')->with(['inputBy', 'project']);

    // Apply Mode of Payment filter
    if ($request->has('status') && $request->status !== 'all') {
        $query->whereRaw("LOWER(mode_of_payment) = ?", [strtolower($request->status)]);
    }

    // Fetch paginated results first
    $transactions = $query->latest()->paginate(8)->appends($request->query());

    // Decrypt amounts and convert date
    $transactions->getCollection()->transform(function ($transaction) {
        $transaction->amount = (float) Crypt::decryptString($transaction->amount);
        $transaction->date = \Carbon\Carbon::parse($transaction->date); // Convert to Carbon
        return $transaction;
    });

    // Apply search filter
    if ($request->has('search') && !empty($request->search)) {
        $search = strtolower($request->search);

        // Filter transactions after decryption
        $filteredTransactions = $transactions->getCollection()->filter(function ($transaction) use ($search) {
            $transactionAmount = (string) $transaction->amount;
            $modeOfPayment = strtolower($transaction->mode_of_payment ?? '');
            $remarks = strtolower($transaction->remarks ?? '');
            $transactionDate = strtolower($transaction->date->format('Y-m-d')); // 2025-03-13
            $transactionFullDate = strtolower($transaction->date->format('F j, Y')); // March 13, 2025
            $inputBy = strtolower(optional($transaction->inputBy)->firstname . ' ' . optional($transaction->inputBy)->lastname);
            $projectName = strtolower(optional($transaction->project)->name ?? '');

            return str_contains($modeOfPayment, $search) ||
                   str_contains($remarks, $search) ||
                   str_contains($transactionDate, $search) ||
                   str_contains($transactionFullDate, $search) ||
                   str_contains($transactionAmount, $search) ||  // Searching by amount
                   str_contains($inputBy, $search) ||
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

    return view('user.Income.Income', compact('transactions', 'transaction'));
}






    public function ViewAddIncomePage()
    {
        $accounts = Accounts::all();
        $projects = Project::where('status', 'ongoing')->get();

        return view('user.Income.AddIncome', compact('projects', 'accounts'));
    }

    public function storeIncome(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0',
            'mode_of_payment' => 'required|string',
            'project_id' => 'required|exists:projects,id',
            'remarks' => 'nullable|string',
        ]);

        $inputById = Auth::id();
        $currentDate = Carbon::now()->toDateString();

        Income::create([
            'amount' => Crypt::encryptString($request->amount), // Encrypt manually
            'input_by' => $inputById,
            'date' => $currentDate,
            'mode_of_payment' => $request->mode_of_payment,
            'project_id' => $request->project_id,
            'remarks' => $request->remarks,
        ]);

        return redirect()->route('ViewIncomePage')->with('success', 'Income added successfully.');
    }

    public function ViewEditIncomePage(Request $request)
    {
        $income = Income::find($request->income_id);

        if ($income) {
            $income->amount = Crypt::decryptString($income->amount); // Decrypt amount before passing to view
        }

        $accounts = Accounts::all();
        $projects = Project::where('status', 'ongoing')->get();

        return view('user.Income.EditIncome', compact('projects', 'accounts', 'income'));
    }

    public function storeEditIncome(Request $request)
    {
        $validated = $request->validate([
            'income_id' => 'required|exists:income,id',
            'amount' => 'required|numeric|min:0',
            'mode_of_payment' => 'required|string',
            'project_id' => 'required|exists:projects,id',
            'remarks' => 'nullable|string',
        ]);

        $income = Income::find($validated['income_id']);

        $income->update([
            'amount' => Crypt::encryptString($request->amount), // Encrypt amount before saving
            'mode_of_payment' => $request->mode_of_payment,
            'project_id' => $request->project_id,
            'remarks' => $request->remarks,
        ]);

        return redirect()->route('ViewIncomePage')->with('success', 'Income updated successfully.');
    }

    public function updateIncomeStatusRemoved(Request $request)
    {
        $validated = $request->validate([
            'income_id' => 'required|exists:income,id',
        ]);

        $income = Income::find($validated['income_id']);

        $removedById = Auth::id();

        $income->update([
            'status' => 'removed',
            'removed_by' => $removedById,
            'date_removed' => now(),
        ]);

        return redirect()->route('ViewIncomePage')->with('success', 'Income removed successfully!');
    }
}
