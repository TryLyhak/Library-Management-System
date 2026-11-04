<x-layouts.app title="Books">

    {{-- Page header with the "Add" button --}}
    <div class="mb-6 flex items-end justify-between gap-4">
        <div>
            <p class="mb-2 text-xs font-semibold uppercase tracking-[0.18em] text-indigo-600">Collection</p>
            <h1 class="text-3xl font-bold tracking-tight text-slate-900">Books</h1>
        </div>
        <a href="{{ route('books.create') }}" class="btn-primary">
            + Add Book
        </a>
    </div>

    {{-- Search and filter form. It uses GET, so the search shows in the URL. --}}
    <form method="GET" action="{{ route('books.index') }}" class="card mb-6 flex flex-col gap-3 p-4 sm:flex-row" data-live-filter>

        <input type="text" name="search" data-live-filter-input data-filter-target="books-table" value="{{ $search }}" placeholder="Search title, author or ISBN..."
            class="field sm:flex-1">

        <select name="category_id" data-live-filter-category data-filter-target="books-table" class="field sm:w-auto">
            <option value="">All categories</option>
            @foreach ($categories as $category)
                {{-- "selected" keeps the chosen category after searching --}}
                <option value="{{ $category->id }}" @selected($categoryId === $category->id)>
                    {{ $category->name }}
                </option>
            @endforeach
        </select>

        <button type="submit" class="btn-primary">
            Search
        </button>

        {{-- Show a Clear link only when a search or filter is active --}}
        @if ($search || $categoryId)
            <a href="{{ route('books.index') }}" class="btn-secondary">Clear</a>
        @endif
    </form>

    {{-- overflow-x-auto lets the table scroll sideways on small phones --}}
    <div class="table-card">
        <table id="books-table" class="data-table" data-live-filter-table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Author</th>
                    <th>ISBN</th>
                    <th>Category</th>
                    <th>Available</th>
                    <th class="text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y">

                @forelse ($books as $book)
                    <tr data-filter-row data-filter-category="{{ $book->category_id }}">
                        <td class="px-4 py-3 font-mono text-slate-400">{{ $book->id }}</td>
                        <td class="px-4 py-3 font-medium">{{ $book->title }}</td>
                        <td class="px-4 py-3">{{ $book->author }}</td>
                        <td class="px-4 py-3">{{ $book->isbn }}</td>
                        <td class="px-4 py-3">{{ $book->category->name }}</td>
                        <td class="px-4 py-3">
                            {{-- Green badge if copies are available, red if all are borrowed --}}
                            <x-badge :type="$book->available_copies > 0 ? 'success' : 'danger'">
                                {{ $book->available_copies }} / {{ $book->total_copies }}
                            </x-badge>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex justify-end gap-3">
                                <a href="{{ route('books.edit', $book) }}" class="btn-secondary px-3 py-2">Edit</a>

                                <form method="POST" action="{{ route('books.destroy', $book) }}"
                                    data-confirm-form
                                    data-confirm-title="Delete book?"
                                    data-confirm-message="This will permanently remove {{ $book->title }}. This action cannot be undone.">
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
                        <td colspan="6" class="px-4 py-6 text-center text-gray-500">
                            No books found.
                        </td>
                    </tr>
                @endforelse
                <tr data-filter-empty hidden>
                    <td colspan="6" class="px-4 py-10 text-center text-slate-400">
                        No matching books found.
                    </td>
                </tr>

            </tbody>
        </table>
    </div>

    {{-- Page number links (withQueryString() in the controller keeps the search) --}}
    <div class="mt-4">
        {{ $books->links() }}
    </div>

</x-layouts.app>
