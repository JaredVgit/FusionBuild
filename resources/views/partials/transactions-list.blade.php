<ul id="transactionList" class="space-y-2 divide-y divide-gray-200">
    @forelse($transactions as $transaction)
        <li class="flex justify-between items-center py-3 transaction-item" 
            data-type="{{ $transaction instanceof App\Models\Income ? 'income' : 'expense' }}" 
            data-project="{{ strtolower($transaction->project->name ?? '') }}"
            data-amount="{{ $transaction->amount }}"
            data-date="{{ $transaction->created_at->format('Y-m-d') }}"
            data-date-human="{{ strtolower($transaction->created_at->format('F')) }}">

            <div>
                <span class="text-gray-700 font-medium">
                    {{ $transaction instanceof App\Models\Income ? 'Income from' : 'Expense for' }}
                    <span class="font-semibold">{{ $transaction->project->name ?? 'No Project' }}</span>
                </span>
                <p class="text-sm text-gray-500">
                    {{ $transaction->created_at->format('F j, Y - g:i A') }}
                </p>
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

<!-- Pagination -->
<div class="mt-4">
    {{ $transactions->links() }}
</div>

