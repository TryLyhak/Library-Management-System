<x-layouts.app title="Members">

    {{-- Page header with the "Add" button --}}
    <div class="mb-6 flex items-end justify-between gap-4">
        <div>
            <p class="mb-2 text-xs font-semibold uppercase tracking-[0.18em] text-indigo-600">Community</p>
            <h1 class="text-3xl font-bold tracking-tight text-slate-900">Members</h1>
        </div>
        <a href="{{ route('members.create') }}"
           class="btn-primary">
            + Add Member
        </a>
    </div>

    {{-- Search form. It uses GET, so the search shows in the URL. --}}
    <form method="GET" action="{{ route('members.index') }}"
          class="card mb-6 flex flex-col gap-3 p-4 sm:flex-row" data-live-filter>

        <input type="text" name="search" data-live-filter-input data-filter-target="members-table" value="{{ $search }}"
               placeholder="Search name, email or member ID..."
               class="field sm:flex-1">

        <button type="submit" class="btn-primary">
            Search
        </button>

        {{-- Show a Clear link only when a search is active --}}
        @if ($search)
            <a href="{{ route('members.index') }}"
               class="btn-secondary">Clear</a>
        @endif
    </form>

    {{-- overflow-x-auto lets the table scroll sideways on small phones --}}
    <div class="table-card">
        <table id="members-table" class="data-table" data-live-filter-table>
            <thead>
                <tr>
                    <th class="px-4 py-3">ID</th>
                    <th class="px-4 py-3">Name</th>
                    <th class="px-4 py-3">Email</th>
                    <th class="px-4 py-3">Phone</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3">Borrowed</th>
                    <th class="px-4 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y">

                @forelse ($members as $member)
                    <tr data-filter-row>
                        <td class="px-4 py-3 font-mono text-slate-500">#{{ $member->id }}</td>
                        <td class="px-4 py-3 font-medium">{{ $member->full_name }}</td>
                        <td class="px-4 py-3">{{ $member->email }}</td>
                        <td class="px-4 py-3">{{ $member->phone ?? '-' }}</td>
                        <td class="px-4 py-3">
                            {{-- Green badge for active members, gray for inactive --}}
                            <span class="rounded px-2 py-1 text-xs font-semibold
                                {{ $member->is_active ? 'bg-black text-white' : 'bg-slate-100 text-slate-600' }}">
                                {{ $member->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        {{-- active_loans_count comes from withCount() in the controller --}}
                        <td class="px-4 py-3">{{ $member->active_loans_count }}</td>
                        <td class="px-4 py-3">
                            <div class="flex justify-end gap-3">
                                <a href="{{ route('members.edit', $member) }}"
                                   class="btn-secondary px-3 py-2">Edit</a>

                                <form method="POST"
                                      action="{{ route('members.destroy', $member) }}"
                                      data-confirm-form
                                      data-confirm-title="Delete member?"
                                      data-confirm-message="This will permanently remove {{ $member->full_name }}. This action cannot be undone.">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-danger px-3 py-2">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-4 py-6 text-center text-gray-500">
                            No members found.
                        </td>
                    </tr>
                @endforelse
                <tr data-filter-empty hidden>
                    <td colspan="8" class="px-4 py-10 text-center text-slate-400">
                        No matching members found.
                    </td>
                </tr>

            </tbody>
        </table>
    </div>

    {{-- Page number links (withQueryString() in the controller keeps the search) --}}
    <div class="mt-4">
        {{ $members->links() }}
    </div>

</x-layouts.app>