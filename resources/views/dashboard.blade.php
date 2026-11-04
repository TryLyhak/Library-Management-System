<x-layouts.app title="Dashboard">

    {{-- Pick the greeting from the current hour (uses the Asia/Phnom_Penh timezone we set) --}}
    @php
        $hour = now()->hour;

        // match(true) checks each line from top to bottom and uses the first one that is true
        $greeting = match (true) {
            $hour < 12 => 'Good morning',
            $hour < 18 => 'Good afternoon',
            default => 'Good evening',
        };
    @endphp

    <div class="relative mb-8 overflow-hidden rounded-3xl bg-slate-950 px-6 py-7 text-white shadow-xl shadow-slate-200/60 sm:px-8 sm:py-9">
        <div class="absolute -right-20 -top-32 h-80 w-80 rounded-full bg-indigo-500/25 blur-3xl"></div>
        <div class="absolute -bottom-40 left-1/3 h-80 w-80 rounded-full bg-violet-500/15 blur-3xl"></div>
        <div class="relative flex flex-col justify-between gap-6 md:flex-row md:items-end">
        <div>
            <p class="mb-3 text-xs font-semibold uppercase tracking-[0.2em] text-indigo-300">Your library at a glance</p>
            <h1 class="text-3xl font-bold tracking-tight sm:text-4xl">{{ $greeting }}, {{ auth()->user()->name }}</h1>
            <p class="mt-3 max-w-xl text-sm leading-6 text-slate-300">Here is what is happening across your library today. Keep your collection moving and your members supported.</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('loans.create') }}" class="btn-primary bg-white px-5 py-3 text-indigo-700 hover:bg-indigo-50">
                <span class="text-lg">+</span> Record a loan
            </a>
            <a href="{{ route('books.create') }}" class="inline-flex items-center justify-center gap-2 rounded-xl border border-white/20 px-5 py-3 text-sm font-semibold text-white transition hover:bg-white/10">
                Add a book
            </a>
        </div>
        </div>
    </div>

    <div class="mb-8 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <a href="{{ route('books.index') }}" class="group card p-5 transition hover:-translate-y-1 hover:border-indigo-200 hover:shadow-lg">
            <div class="mb-6 flex items-start justify-between">
                <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-indigo-50 text-xl text-indigo-600">▦</span>
                <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-500">Library total</span>
            </div>
            <p class="text-sm font-medium text-slate-500">Book titles</p>
            <p class="mt-1 text-3xl font-bold text-slate-900">{{ $stats['book_titles'] }}</p>
            <p class="mt-2 text-xs text-slate-400">{{ $stats['available_copies'] }} of {{ $stats['total_copies'] }} copies available</p>
        </a>

        <a href="{{ route('members.index') }}" class="group card p-5 transition hover:-translate-y-1 hover:border-indigo-200 hover:shadow-lg">
            <div class="mb-6 flex items-start justify-between">
                <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-violet-50 text-xl text-violet-600">◎</span>
                <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-500">Members</span>
            </div>
            <p class="text-sm font-medium text-slate-500">Active members</p>
            <p class="mt-1 text-3xl font-bold text-slate-900">{{ $stats['active_members'] }}</p>
            <p class="mt-2 text-xs text-slate-400">Currently registered and active</p>
        </a>

        <a href="{{ route('loans.index', ['status' => 'active']) }}" class="group card p-5 transition hover:-translate-y-1 hover:border-indigo-200 hover:shadow-lg">
            <div class="mb-6 flex items-start justify-between">
                <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-sky-50 text-xl text-sky-600">↗</span>
                <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-500">In circulation</span>
            </div>
            <p class="text-sm font-medium text-slate-500">Borrowed now</p>
            <p class="mt-1 text-3xl font-bold text-slate-900">{{ $stats['borrowed_now'] }}</p>
            <p class="mt-2 text-xs text-slate-400">Books currently with members</p>
        </a>

        <a href="{{ route('loans.index', ['status' => 'overdue']) }}"
           class="group card p-5 transition hover:-translate-y-1 hover:shadow-lg {{ $stats['overdue'] > 0 ? 'border-amber-200 bg-amber-50/50' : '' }}">
            <div class="mb-6 flex items-start justify-between">
                <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-amber-100 text-xl text-amber-700">!</span>
                <span class="rounded-full bg-amber-100 px-2.5 py-1 text-xs font-semibold text-amber-700">Needs attention</span>
            </div>
            <p class="text-sm font-medium text-slate-500">Overdue loans</p>
            <p class="mt-1 text-3xl font-bold text-slate-900">{{ $stats['overdue'] }}</p>
            <p class="mt-2 text-xs text-slate-400">Follow up with members</p>
        </a>
    </div>

    <div class="mb-4 flex items-end justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-900">Recent activity</h2>
            <p class="mt-1 text-sm text-slate-500">The latest borrowing activity in your library.</p>
        </div>
        <a href="{{ route('loans.index') }}" class="btn-link whitespace-nowrap">View all loans <span aria-hidden="true">→</span></a>
    </div>

    <div class="table-card">
        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-sm">
            <thead class="border-b border-slate-100 bg-slate-50/70 text-xs uppercase tracking-wider text-slate-400">
                <tr>
                    <th class="px-6 py-4 font-semibold">Book</th>
                    <th class="px-6 py-4 font-semibold">Member</th>
                    <th class="px-6 py-4 font-semibold">Due date</th>
                    <th class="px-6 py-4 font-semibold">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($recentLoans as $loan)
                    @php
                        $isOverdue = $loan->returned_date === null && $loan->due_date->lt(today());
                    @endphp
                    <tr class="transition hover:bg-slate-50">
                        <td class="px-6 py-4 font-semibold text-slate-800">{{ $loan->book->title }}</td>
                        <td class="px-6 py-4 text-slate-500">{{ $loan->member->full_name }}</td>
                        <td class="px-6 py-4 text-slate-500">{{ $loan->due_date->format('d/m/Y') }}</td>
                        <td class="px-6 py-4">
                            @if ($loan->returned_date)
                                <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">Returned</span>
                            @elseif ($isOverdue)
                                <span class="rounded-full bg-black px-3 py-1 text-xs font-semibold text-white">Overdue</span>
                            @else
                                <span class="rounded-full bg-slate-200 px-3 py-1 text-xs font-semibold text-slate-700">Borrowed</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center text-slate-400">No loans yet.</td>
                    </tr>
                @endforelse
            </tbody>
            </table>
        </div>
    </div>

</x-layouts.app>