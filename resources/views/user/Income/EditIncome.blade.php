@extends('layouts.app')

@section('title', 'Edit Income | FusionBuild')

@section('content')
<div class="flex justify-center items-center min-h-screen">
    <div class="bg-grayLight p-6 md:p-8 rounded-lg shadow-lg w-full sm:w-3/4 lg:w-1/2">
        <h2 class="text-2xl font-bold text-primary mb-6 text-black">Edit Income</h2>

        @if ($errors->any())
            <div class="bg-red-500 text-white p-3 rounded-lg mb-4">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>- {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('storeEditIncome', $income->id) }}" method="POST" class="space-y-4">
            @csrf
            @method('POST')

            <input type="hidden" name="income_id" value="{{ $income->id }}">

            <div class="space-y-2">
                <label for="amount" class="block text-black font-semibold">Amount (₱)</label>
                <input type="text" name="amount" id="amount" class="w-full p-2 border rounded-lg text-black"
    required placeholder="Enter amount" value="{{ old('amount', $income->amount) }}"
    oninput="validateAmount(this)">


            <div class="space-y-2">
                <label for="mode_of_payment" class="block text-black font-semibold">Mode of Payment</label>
                <select name="mode_of_payment" id="mode_of_payment" class="w-full p-2 border rounded-lg text-black" required>
                    <option value="" disabled>Select Payment Mode</option>
                    <option value="cash" {{ old('mode_of_payment', $income->mode_of_payment) == 'cash' ? 'selected' : '' }}>Cash</option>
                    <option value="bank transfer" {{ old('mode_of_payment', $income->mode_of_payment) == 'bank transfer' ? 'selected' : '' }}>Bank Transfer</option>
                    <option value="gcash" {{ old('mode_of_payment', $income->mode_of_payment) == 'gcash' ? 'selected' : '' }}>GCash</option>
                    <option value="check" {{ old('mode_of_payment', $income->mode_of_payment) == 'check' ? 'selected' : '' }}>Check</option>
                </select>
            </div>

            <div class="space-y-2">
                <label for="project_id" class="block text-black font-semibold">Project</label>
                <select name="project_id" id="project_id" class="w-full p-2 border rounded-lg text-black" required>
                    <option value="" disabled>Select Project</option>
                    @foreach($projects as $project)
                        <option value="{{ $project->id }}" {{ old('project_id', $income->project_id) == $project->id ? 'selected' : '' }}>
                            {{ $project->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="space-y-2">
                <label for="remarks" class="block text-black font-semibold">Remarks</label>
                <textarea name="remarks" id="remarks" class="w-full p-2 border rounded-lg text-black" placeholder="Enter remarks">{{ old('remarks', $income->remarks) }}</textarea>
            </div>

            <div class="flex justify-end space-x-2">
                <a href="/income" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition">Cancel</a>
                <button type="submit" class="bg-mustardOrange text-black px-4 py-2 rounded-lg font-semibold hover:bg-opacity-80 transition" 
    onclick="return confirm('Are you sure you want to update this income record?');">
    Update Income
</button>

            </div>
        </form>
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
</script>
@endsection