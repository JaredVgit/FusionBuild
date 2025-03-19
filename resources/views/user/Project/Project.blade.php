@extends('layouts.app')

@section('title', 'Projects | FusionBuild')

@section('content')
<div class="bg-grayDarker p-8 rounded-lg ">
    @if(session('success'))
        <div class="bg-green-500 text-white p-4 rounded-lg mb-6">
            {{ session('success') }}
        </div>
    @endif

    <h2 class="text-2xl font-bold text-primary mb-6 text-black">Projects Overview</h2>

    <!-- Funds Summary -->
    <div class="bg-grayLight p-6 rounded-lg text-textLight flex justify-between items-center mb-8 shadow-md">
        <div>
            <h3 class="text-lg text-black">Total Projects</h3>
            <p class="text-4xl font-bold text-primary text-black">{{ $project->where('status', '!=', 'removed')->count() }}</p>
        </div>
        <a href="/projects-add" class="bg-mustardOrange text-black px-5 py-3 rounded-lg font-semibold hover:bg-opacity-80 transition shadow-md">
            Add Project
        </a>
    </div>
    
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white text-black p-6 rounded-lg shadow-lg ">
        <h2 class="text-lg font-semibold text-darkBlue mb-2">Pending Projects</h2>
        <p class="text-3xl font-bold">{{ $project->where('status', 'pending')->count() }}</p>
    </div>
    
    <div class="bg-white text-black p-6 rounded-lg shadow-lg ">
        <h2 class="text-lg font-semibold text-darkBlue mb-2">Ongoing Projects</h2>
        <p class="text-3xl font-bold">{{ $project->where('status', 'ongoing')->count() }}</p>
    </div>
    
    <div class="bg-white text-black p-6 rounded-lg shadow-lg ">
        <h2 class="text-lg font-semibold text-darkBlue mb-2">Completed Projects</h2>
        <p class="text-3xl font-bold">{{ $project->where('status', 'completed')->count() }}</p>
    </div>
    
    <div class="bg-white text-black p-6 rounded-lg shadow-lg ">
        <h2 class="text-lg font-semibold text-darkBlue mb-2">Cancelled Projects</h2>
        <p class="text-3xl font-bold">{{ $project->where('status', 'cancelled')->count() }}</p>
    </div>
</div>


    <!-- Filter & Search -->
<div class="px">
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-6 space-y-4 md:space-y-0">
        <!-- Filter by Status -->
        <div>
            <label for="statusFilter" class="text-black font-semibold">Filter by Status:</label>
            <select id="statusFilter" class="border p-2 text-black rounded-lg">
                <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>All</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="ongoing" {{ request('status') == 'ongoing' ? 'selected' : '' }}>Ongoing</option>
                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
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
</div>

    <!-- Project Table -->
    <div class="bg-grayLight p-6 rounded-lg shadow-md">
        <h3 class="text-lg font-semibold text-primary mb-4 text-black">Project History</h3>
        <div class="overflow-x-auto border rounded-lg">
        <table class="w-full text-left border-collapse table-auto" id="projectsTable">
    <thead>
        <tr class="bg-sidebar text-white bg-[#001f3f] text-white rounded-t-lg">
    <th class="py-3 px-1 whitespace-nowrap text-center">ID</th>
    <th class="py-4 px-6 whitespace-nowrap text-center">PROJECT NAME</th>
    <th class="py-4 px-6 whitespace-nowrap text-center">COORDINATOR</th>
    <th class="py-4 px-6 whitespace-nowrap text-center">REMARKS</th>
    <th class="py-4 px-6 whitespace-nowrap text-center">START DATE</th>
    <th class="py-4 px-8 whitespace-nowrap text-center">END DATE</th>
    <th class="py-4 px-6 whitespace-nowrap text-center">STATUS</th>
    <th class="py-4 px-6 whitespace-nowrap text-center">ACTION</th>
</tr>

    </thead>
    <tbody>
        @forelse ($projects as $project)
            <tr class="border-b text-black project-row" data-status="{{ strtolower($project->status) }}">
                <td class="py-3 px-4 cursor-pointer" onclick="openProjectModal({{ json_encode($project) }})">{{ $project->id }}</td>
                <td class="py-3 px-6 break-words cursor-pointer" onclick="openProjectModal({{ json_encode($project) }})">{{ $project->name }}</td>
                <td class="py-3 px-6 break-words ">{{ $project->coordinator }}</td>
                <td class="py-3 px-6 break-words">{{ $project->remarks }}</td>
                <td class="py-3 px-6 text-center">
    {!! $project->start_date ? date('M d, Y', strtotime($project->start_date)) : '<span class="text-gray-500">N/A</span>' !!}
</td>
<td class="py-3 px-6 text-center">
    {!! $project->end_date ? date('M d, Y', strtotime($project->end_date)) : '<span class="text-gray-500">N/A</span>' !!}
</td>

                <td class="py-3 px-6">
    <span class="px-4 py-2 text-sm font-semibold rounded-lg text-white inline-block text-center w-24
        {{ strtolower($project->status) == 'pending' ? 'bg-yellow-500' : 
        (strtolower($project->status) == 'ongoing' ? 'bg-blue-500' : 
        (strtolower($project->status) == 'cancelled' ? 'bg-red-500' : 'bg-green-500')) }}">
        {{ ucfirst($project->status) }}
    </span>
</td>
<td class="py-3 px-6 whitespace-nowrap text-center">
    @if ($project->status == 'pending')
        <form action="{{ route('projects.updateStatusStart') }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to start this project?');">
            @csrf
            <input type="hidden" name="project_id" value="{{ $project->id }}">
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 text-sm font-semibold rounded-lg w-24 text-center hover:bg-blue-600 transition">
                Start
            </button>
        </form>
        <form action="{{ route('projects.updateStatusCancel') }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to cancel this project?');">
            @csrf
            <input type="hidden" name="project_id" value="{{ $project->id }}">
            <button type="submit" class="bg-red-500 text-white px-4 py-2 text-sm font-semibold rounded-lg w-24 text-center hover:bg-red-600 transition">
                Cancel
            </button>
        </form>
        <form action="{{ route('ViewEditProjectPage') }}" method="GET" class="inline">
    @csrf
    <input type="hidden" name="project_id" value="{{ $project->id }}">
    <button type="submit" class="bg-[#001f3f] text-white px-4 py-2 text-sm font-semibold rounded-lg w-24 text-center hover:bg-[#001a35] transition">
        Edit
    </button>
</form>
    @elseif ($project->status == 'ongoing')
        <form action="{{ route('projects.updateStatusDone') }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to mark this project as done?');">
            @csrf
            <input type="hidden" name="project_id" value="{{ $project->id }}">
            <button type="submit" class="bg-green-500 text-white px-4 py-2 text-sm font-semibold rounded-lg w-24 text-center hover:bg-green-600 transition">
                Done
            </button>
        </form>
        <form action="{{ route('projects.updateStatusCancel') }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to cancel this project?');">
    @csrf
    <input type="hidden" name="project_id" value="{{ $project->id }}">
    <button type="submit" class="bg-red-500 text-white px-4 py-2 text-sm font-semibold rounded-lg w-24 text-center hover:bg-red-600 transition">
        Cancel
    </button>
</form>
        <form action="{{ route('ViewEditProjectPage') }}" method="GET" class="inline">
    @csrf
    <input type="hidden" name="project_id" value="{{ $project->id }}">
    <button type="submit" class="bg-[#001f3f] text-white px-4 py-2 text-sm font-semibold rounded-lg w-24 text-center hover:bg-[#001a35] transition">
        Edit
    </button>
</form>

    @elseif ($project->status == 'completed')
        <span class="text-gray-500 w-24 inline-block text-center">Completed</span>
        @elseif ($project->status == 'cancelled')
        <form action="{{ route('projects.updateStatusRestore') }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to restore this project?');">
    @csrf
    <input type="hidden" name="project_id" value="{{ $project->id }}">
    <button type="submit" class="bg-yellow-500 text-white px-4 py-2 text-sm font-semibold rounded-lg w-24 text-center hover:bg-yellow-600 transition">
        Restore
    </button>
</form>


    <form action="{{ route('projects.updateStatusRemoved') }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to remove this project? This action will also remove all associated transactions and cannot be undone.');">
        @csrf
        <input type="hidden" name="project_id" value="{{ $project->id }}">
        <button type="submit" class="bg-red-500 text-white px-4 py-2 text-sm font-semibold rounded-lg w-24 text-center hover:bg-red-600 transition">
            Remove
        </button>
    </form>
@endif

</td>
            </tr>
        @empty
        <tr class="bg-gray-600 text-white text-center">
    <td class="py-3 px-4 border border-gray-500" colspan="8">No Projects Available</td>
</tr>

        @endforelse
    </tbody>
</table>

    </div>
    <!-- Pagination -->
<div class="mt-4">
    {{ $projects->links() }}
</div>
</div>

<!-- Project Details Modal -->
<div id="projectModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden">
    <div class="bg-white p-6 rounded-lg shadow-lg max-w-lg w-full">
        <!-- Modal Header -->
        <div class="pb-4">
            <h2 class="text-2xl font-bold text-black" id="modalProjectName"></h2>
        </div>

        <!-- Project Details Section -->
        <div class="p-4 border rounded-lg bg-gray-50 shadow">
            <div class="mb-4">
                <p class="text-lg font-semibold text-gray-700">Income:</p>
                <p class="text-xl text-black font-bold" id="modalProjectIncome"></p>
            </div>

            <div class="mb-4">
                <p class="text-lg font-semibold text-gray-700">Expenses:</p>
                <p class="text-xl text-black font-bold" id="modalProjectExpenses"></p>
            </div>

            <div>
                <p class="text-lg font-semibold text-gray-700">Balance:</p>
                <p class="text-xl text-green-600 font-bold" id="modalProjectBalance"></p>
            </div>
        </div>

        <!-- Close Button Aligned to the Right -->
        <div class="flex justify-end mt-6">
            <button onclick="closeProjectModal()" 
                class="bg-red-500 text-white px-5 py-2 rounded-lg font-semibold hover:bg-red-600 transition">
                Close
            </button>
        </div>
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



    function openProjectModal(project) {
    document.getElementById("modalProjectName").innerText = project.name;
    document.getElementById("modalProjectIncome").innerText = `₱${(project.total_income ?? 0).toLocaleString('en-PH', { minimumFractionDigits: 2 })}`;
    document.getElementById("modalProjectExpenses").innerText = `₱${(project.total_expenses ?? 0).toLocaleString('en-PH', { minimumFractionDigits: 2 })}`;

    let balanceElement = document.getElementById("modalProjectBalance");
    let balanceValue = project.total_balance ?? 0; 
    balanceElement.innerText = `₱${balanceValue.toLocaleString('en-PH', { minimumFractionDigits: 2 })}`;

    // Remove existing color classes
    balanceElement.classList.remove("text-green-600", "text-black", "text-red-600");

    // Apply the correct color to income
    if (balanceValue > 0) {
        balanceElement.classList.add("text-green-600"); // Green if above 0
    } else if (balanceValue < 0) {
        balanceElement.classList.add("text-red-600"); // Red if below 0
    } else {
        balanceElement.classList.add("text-black"); // Black if exactly 0
    }

    document.getElementById("projectModal").classList.remove("hidden");
}



    function closeProjectModal() {
        document.getElementById("projectModal").classList.add("hidden");
    }


</script>
@endsection