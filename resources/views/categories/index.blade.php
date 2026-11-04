<x-layouts.app title="Categories">

    <div class="mb-6 flex items-end justify-between gap-4">
        <div>
            <p class="mb-2 text-xs font-semibold uppercase tracking-[0.18em] text-indigo-600">Collection</p>
            <h1 class="text-3xl font-bold tracking-tight text-slate-900">Categories</h1>
        </div>
        <a href="{{ route('categories.create') }}"
           class="btn-primary">
            + Add Category
        </a>
    </div>

    <form method="GET" action="{{ route('categories.index') }}" class="card mb-6 flex flex-col gap-3 p-4 sm:flex-row" data-live-filter>
        <div class="relative flex-1">
            <label for="category-search" class="sr-only">Search categories</label>
            <input id="category-search" type="search" name="search" data-live-filter-input data-filter-target="categories-table" value="{{ $search }}"
                   placeholder="Search category name or description..."
                   class="field pl-10" autocomplete="off">
            <span class="pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-slate-400" aria-hidden="true">⌕</span>
        </div>
        @if ($search)
            <a href="{{ route('categories.index') }}" class="btn-secondary">Clear</a>
        @endif
    </form>

    <div class="table-card">
        <table id="categories-table" class="data-table" data-live-filter-table>
            <thead>
                <tr>
                    <th class="px-4 py-3">Name</th>
                    <th class="px-4 py-3">Description</th>
                    <th class="px-4 py-3">Books</th>
                    <th class="px-4 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y">

                {{-- @forelse loops over the items, and runs @empty if there are none --}}
                @forelse ($categories as $category)
                    <tr data-filter-row>
                        <td class="px-4 py-3 font-medium">{{ $category->name }}</td>
                        <td class="px-4 py-3">{{ $category->description ?? '-' }}</td>
                        {{-- books_count comes from withCount('books') in the controller --}}
                        <td class="px-4 py-3">{{ $category->books_count }}</td>
                        <td class="px-4 py-3">
                            <div class="flex justify-end gap-3">
                                <a href="{{ route('categories.edit', $category) }}"
                                   class="btn-secondary px-3 py-2">Edit</a>

                                {{-- Delete needs a form, because browsers can't send DELETE from a link --}}
                                <form method="POST"
                                      action="{{ route('categories.destroy', $category) }}"
                                      data-confirm-form
                                      data-confirm-title="Delete category?"
                                      data-confirm-message="This will permanently remove {{ $category->name }}. This action cannot be undone.">
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
                        <td colspan="4" class="px-4 py-6 text-center text-gray-500">
                            No categories yet.
                        </td>
                    </tr>
                @endforelse
                <tr data-filter-empty hidden>
                    <td colspan="4" class="px-4 py-10 text-center text-slate-400">
                        No matching categories found.
                    </td>
                </tr>

            </tbody>
        </table>
    </div>

    {{-- Page number links (we used paginate(10) in the controller) --}}
    <div class="mt-4">
        {{ $categories->links() }}
    </div>

</x-layouts.app>