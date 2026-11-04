<x-layouts.app title="Loans">

    {{-- Page header with the "Borrow" button --}}
    <div class="mb-6 flex items-end justify-between gap-4">
        <div>
            <p class="mb-2 text-xs font-semibold uppercase tracking-[0.18em] text-indigo-600">Circulation</p>
            <h1 class="text-3xl font-bold tracking-tight text-slate-900">Loans</h1>
        </div>
        <a href="{{ route('loans.create') }}"
           class="btn-primary">
            + Borrow a Book
        </a>
    </div>

    {{-- Filter tabs. Each link sends ?status=... to the controller. --}}
    @php
        $tabs = [
            '' => 'All',
            'active' => 'Borrowed',
            'overdue' => 'Overdue',
            'returned' => 'Returned',
        ];
    @endphp

    <div class="mb-6 flex flex-wrap gap-2">
        @foreach ($tabs as $value => $label)
            <a href="{{ route('loans.index', $value ? ['status' => $value] : []) }}"
               class="rounded-xl border px-4 py-2 text-sm font-semibold transition
                      {{ $status === $value ? 'border-black bg-black text-white' : 'border-slate-200 bg-white text-slate-600 hover:border-black hover:text-black' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    <form method="GET" action="{{ route('loans.index') }}" class="card mb-6 flex flex-col gap-3 p-4 sm:flex-row" data-live-filter>
        <input type="hidden" name="status" value="{{ $status }}">
        <div class="relative flex-1">
            <label for="loan-search" class="sr-only">Search loans</label>
            <input id="loan-search" type="search" name="search" data-live-filter-input data-filter-target="loans-table" value="{{ $search }}"
                   placeholder="Search by book, member"
                   class="field pl-10" autocomplete="off">
            <span class="pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-slate-400" aria-hidden="true">⌕</span>
        </div>
        @if ($search)
            <a href="{{ route('loans.index', $status ? ['status' => $status] : []) }}" class="btn-secondary">Clear</a>
        @endif
    </form>

    <div class="table-card">
        <table id="loans-table" class="data-table" data-live-filter-table>
            <thead>
                <tr>
                    <th class="px-4 py-3">Book</th>
                    <th class="px-4 py-3">Member</th>
                    <th class="px-4 py-3">Borrowed</th>
                    <th class="px-4 py-3">Due</th>
                    <th class="px-4 py-3">Returned</th>
                    <th class="px-4 py-3">Loan fee</th>
                    <th class="px-4 py-3">Late fine</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3 text-right">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y">

                @forelse ($loans as $loan)
                    {{-- A loan is overdue if it is not returned and the due date has passed --}}
                    @php
                        $isOverdue = $loan->returned_date === null && $loan->due_date->lt(today());
                    @endphp

                    <tr data-filter-row>
                        <td class="px-4 py-3 font-medium">{{ $loan->book->title }}</td>
                        <td class="px-4 py-3">{{ $loan->member->full_name }}</td>
                        <td class="px-4 py-3">{{ $loan->borrowed_date->format('d/m/Y') }}</td>
                        <td class="px-4 py-3 {{ $isOverdue ? 'font-semibold text-black' : '' }}">
                            {{ $loan->due_date->format('d/m/Y') }}
                        </td>
                        <td class="px-4 py-3">{{ $loan->returned_date?->format('d/m/Y') ?? '-' }}</td>
                        <td class="px-4 py-3">
                            {{ $loan->loan_fee > 0 ? '$' . $loan->loan_fee : '-' }}
                        </td>
                        <td class="px-4 py-3">
                            {{ $loan->fine_amount > 0 ? '$' . $loan->fine_amount : '-' }}
                        </td>
                        <td class="px-4 py-3">
                            @if ($loan->returned_date)
                                <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">Returned</span>
                            @elseif ($isOverdue)
                                <span class="rounded-full bg-black px-3 py-1 text-xs font-semibold text-white">Overdue</span>
                            @else
                                <span class="rounded-full bg-slate-200 px-3 py-1 text-xs font-semibold text-slate-700">Borrowed</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex justify-end">
                                {{-- Only show the Return button for loans that are not returned yet --}}
                                @unless ($loan->returned_date)
                                    <form method="POST"
                                          action="{{ route('loans.return', $loan) }}"
                                          data-confirm-form
                                          data-confirm-title="Return book?"
                                          data-confirm-message="Mark {{ $loan->book->title }} as returned?"
                                          data-confirm-label="Return">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn-secondary px-3 py-2">
                                            Return
                                        </button>
                                    </form>
                                @endunless
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="px-4 py-6 text-center text-gray-500">
                            No loans found.
                        </td>
                    </tr>
                @endforelse
                <tr data-filter-empty hidden>
                    <td colspan="9" class="px-4 py-10 text-center text-slate-400">
                        No matching loans found.
                    </td>
                </tr>

            </tbody>
        </table>
    </div>

    {{-- Page number links (withQueryString() in the controller keeps the filter) --}}
    <div class="mt-4">
        {{ $loans->links() }}
    </div>

</x-layouts.app>