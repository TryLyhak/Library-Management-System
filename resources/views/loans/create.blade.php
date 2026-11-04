<x-layouts.app title="Borrow a Book">
    <div class="mb-8">
        <p class="mb-2 text-sm font-semibold uppercase tracking-[0.18em] text-black">Circulation</p>
        <h1 class="text-3xl font-bold tracking-tight text-slate-900">Record a new loan</h1>
        <p class="mt-2 text-slate-500">Choose an available book and an active member.</p>
    </div>

    <div class="max-w-2xl rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
        <form method="POST" action="{{ route('loans.store') }}" class="space-y-6">
            @csrf

            <div>
                <label for="book_id" class="mb-2 block text-sm font-semibold text-slate-700">Book</label>
                @php
                    $selectedBook = $books->firstWhere('id', (int) old('book_id'));
                @endphp
                <div class="relative" data-autocomplete>
                    <input type="hidden" id="book_id" name="book_id" value="{{ old('book_id') }}">
                    <input type="search" id="book_search" value="{{ $selectedBook?->title }}"
                           placeholder="Search available books..." class="field"
                           data-autocomplete-input="book-options" autocomplete="off">
                    <div id="book-options" class="absolute inset-x-0 top-full z-10 mt-2 hidden max-h-64 overflow-y-auto rounded-2xl border border-slate-200 bg-white p-2 shadow-xl shadow-slate-900/10">
                    @foreach ($books as $book)
                        <button type="button" class="flex w-full items-center gap-3 rounded-xl px-3 py-3 text-left transition hover:bg-indigo-50" data-autocomplete-option data-value="{{ $book->id }}" data-name="{{ $book->title }}" data-label="{{ strtolower($book->title) }}" data-available="{{ $book->available_copies }}">
                            {{-- <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-indigo-50 text-indigo-600">▦</span> --}}
                            <span class="min-w-0 flex-1">
                                <span class="block truncate text-sm font-semibold text-slate-800">{{ $book->title }}</span>
                                {{-- <span class="block text-xs text-slate-400">Available to borrow now</span> --}}
                            </span>
                            <span class="shrink-0 rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-bold text-emerald-700">{{ $book->available_copies }} {{ Str::plural('copy', $book->available_copies) }}</span>
                        </button>
                    @endforeach
                    <p data-autocomplete-empty class="hidden px-3 py-4 text-center text-sm text-slate-400">No matching books found.</p>
                    </div>
                </div>
                <p id="book-availability" class="mt-2 hidden text-sm font-medium text-emerald-700"></p>
                @error('book_id')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="member_id" class="mb-2 block text-sm font-semibold text-slate-700">Member</label>
                @php
                    $selectedMember = $members->firstWhere('id', (int) old('member_id'));
                @endphp
                <div class="relative" data-autocomplete>
                    <input type="hidden" id="member_id" name="member_id" value="{{ old('member_id') }}">
                    <input type="search" id="member_search" value="{{ $selectedMember?->full_name }}"
                           placeholder="Search active members..." class="field"
                           data-autocomplete-input="member-options" autocomplete="off">
                    <div id="member-options" class="absolute inset-x-0 top-full z-10 mt-2 hidden max-h-56 overflow-y-auto rounded-xl border border-slate-200 bg-white p-1 shadow-xl">
                    @foreach ($members as $member)
                        <button type="button" class="flex w-full items-start rounded-lg px-3 py-2 text-left text-sm transition hover:bg-indigo-50" data-autocomplete-option data-value="{{ $member->id }}" data-name="{{ $member->full_name }}" data-label="{{ strtolower($member->id.' '.$member->member_code.' '.$member->full_name.' '.$member->email) }}" data-member-id="{{ $member->id }}" data-member-code="{{ $member->member_code }}">
                            <span class="font-semibold text-slate-800">#{{ $member->id }} · {{ $member->member_code }} - {{ $member->full_name }}</span>
                        </button>
                    @endforeach
                    <p data-autocomplete-empty class="hidden px-3 py-3 text-sm text-slate-400">No matching members found.</p>
                    </div>
                </div>
                <p id="member-identity" class="mt-2 hidden text-sm font-medium text-indigo-700"></p>
                @error('member_id')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="due_date" class="mb-2 block text-sm font-semibold text-slate-700">Due date</label>
                <input type="date" id="due_date" name="due_date"
                       value="{{ old('due_date', today()->addDays(7)->toDateString()) }}"
                       min="{{ today()->toDateString() }}" class="field">
                <p class="mt-2 text-sm text-slate-500">The first 7 days are free. Extra days cost $0.25 per day and are charged when the loan is created.</p>
                @error('due_date')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex flex-col-reverse gap-3 border-t border-slate-100 pt-6 sm:flex-row sm:justify-end">
                <a href="{{ route('loans.index') }}" class="btn-secondary">Cancel</a>
                <button type="submit" class="btn-primary">Save loan</button>
            </div>
        </form>
    </div>
</x-layouts.app>
