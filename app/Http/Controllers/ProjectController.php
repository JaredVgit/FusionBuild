<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Auth;
use App\Models\Accounts;
use App\Models\Project;
use App\Models\Income;
use App\Models\Expense;
use Carbon\Carbon;

class ProjectController extends Controller
{
    public function ViewProjectPage(Request $request)
{
    $project = Project::with('incomes', 'expenses')
    ->where('status', '!=', 'removed')
    ->get();
    $query = Project::with('incomes', 'expenses')
    ->where('status', '!=', 'removed')
    ->latest();

    // Apply status filter if selected
    if ($request->has('status') && $request->status !== 'all') {
        $query->where('status', $request->status);
    }

    // Apply search across multiple columns
    if ($request->has('search') && !empty($request->search)) {
        $searchTerm = '%' . $request->search . '%';

        $query->where(function ($q) use ($searchTerm, $request) {
            $q->where('name', 'like', $searchTerm)
              ->orWhere('coordinator', 'like', $searchTerm)
              ->orWhere('status', 'like', $searchTerm)
              ->orWhere('previous_status', 'like', $searchTerm)
              ->orWhere('remarks', 'like', $searchTerm);
            
            // Handle date search
            $parsedDate = strtotime($request->search);
            if ($parsedDate) {
                $formattedDate = date('Y-m-d', $parsedDate);
                $q->orWhereDate('start_date', $formattedDate)
                  ->orWhereDate('end_date', $formattedDate);
            }

            // Handle month name search (e.g., "February")
            $monthNumber = date('m', strtotime($request->search . ' 1')); // Converts "February" to "02"
            if ($monthNumber) {
                $q->orWhereRaw("MONTH(start_date) = ?", [$monthNumber])
                  ->orWhereRaw("MONTH(end_date) = ?", [$monthNumber]);
            }
        });
    }

    // Paginate the results
    $projects = $query->paginate(8)->appends($request->query());

    // Process totals
    foreach ($projects as $project) {
        $income = $project->incomes
            ->where('status', 'active')
            ->sum(fn($income) => $this->safeDecrypt($income->amount));

        $expenses = $project->expenses
            ->where('status', 'active')
            ->sum(fn($expense) => $this->safeDecrypt($expense->amount));

        $project->total_income = $income;
        $project->total_balance = $income - $expenses;
        $project->total_expenses = $expenses;
    }

    return view('user.Project.Project', compact('projects', 'project'));
}




/**
 * Safe decryption function to prevent errors.
 */
private function safeDecrypt($value)
{
    try {
        return (float) Crypt::decryptString($value);
    } catch (\Exception $e) {
        return (float) $value; // If decryption fails, return original value
    }
}

    public function ViewAddProjectPage()
    {
        return view('user.Project.AddProject');
    }

    public function storeProject(Request $request)
{
     $request->validate([
        'name' => 'required|string',
        'coordinator' => 'required|string',
        'remarks' => 'nullable|string',
    ]);

    $Project = Project::create([
        'name' => $request->name,
        'coordinator' => $request->coordinator,
        'remarks' => $request->remarks,
        'status' => 'pending',
    ]);

    return redirect()->route('ViewProjectPage')->with('success', 'Project added successfully.');
}

public function ViewEditProjectPage(Request $request)
    {
        $projects = Project::find($request->project_id);

        return view('user.Project.EditProject', compact('projects'));
    }

public function storeEditProject(Request $request)
    {
        $validated=$request->validate([
            'project_id' => 'required|exists:projects,id',
            'name' => 'required|string',
            'coordinator' => 'required|string',
            'remarks' => 'required|string',
        ]);

        $project = Project::find($validated['project_id']);
    
        $project -> update([
            'name' => $request->name,
            'coordinator' => $request->coordinator,
            'remarks' => $request->remarks,
        ]);
    
        return redirect()->route('ViewProjectPage')->with('success', 'Project updated successfully.');
    }

public function updateStatusStart(Request $request)
{
    $validated = $request->validate([
        'project_id' => 'required|exists:projects,id',
    ]);

    $project = Project::find($validated['project_id']);
    
    // Ensure status is a string
    $project->update([
        'status' => 'ongoing',
        'start_date' => Carbon::today(),
    ]);

    return redirect()->route('ViewProjectPage')->with('success', 'Project status updated successfully!');
}

public function updateStatusCancel(Request $request)
{
    $validated = $request->validate([
        'project_id' => 'required|exists:projects,id',
    ]);

    $project = Project::find($validated['project_id']);
    $previousStatus = $project->status;

    $project->update([
        'previous_status' => $previousStatus,
        'status' => 'cancelled',
    ]);

    return redirect()->route('ViewProjectPage')->with('success', 'Project status updated successfully!');
}

public function updateStatusRestore(Request $request)
{
    $validated = $request->validate([
        'project_id' => 'required|exists:projects,id',
    ]);

    $project = Project::find($validated['project_id']);
    $previousStatus = $project->previous_status;

    $project->update([
        'previous_status' => null,
        'status' => $previousStatus,
    ]);

    return redirect()->route('ViewProjectPage')->with('success', 'Project status updated successfully!');
}

public function updateStatusDone(Request $request)
{
    $validated = $request->validate([
        'project_id' => 'required|exists:projects,id',
    ]);

    $project = Project::find($validated['project_id']);
    
    // Ensure status is a string
    $project->update([
        'status' => 'completed',
        'end_date' => Carbon::today(), 
    ]);

    return redirect()->route('ViewProjectPage')->with('success', 'Project status updated successfully!');
}

public function updateStatusRemoved(Request $request)
{
    $validated = $request->validate([
        'project_id' => 'required|exists:projects,id',
    ]);

    $removedById = Auth::id(); // Get the authenticated user ID once

    // Find the project and update its status
    $project = Project::findOrFail($validated['project_id']);
    $project->update([
        'status'       => 'removed',
        'date_removed' => Carbon::today(),
        'removed_by'   => $removedById,
    ]);

    // Find and update all incomes related to the project
    Income::where('project_id', $project->id)->update([
        'status'       => 'removed',
        'removed_by'   => $removedById,
        'date_removed' => now(),
    ]);

    // Find and update all expenses related to the project
    Expense::where('project_id', $project->id)->update([
        'status'       => 'removed',
        'removed_by'   => $removedById,
        'date_removed' => now(),
    ]);

    return redirect()->route('ViewProjectPage')->with('success', 'Project and related transactions removed successfully!');
}


}
