<x-layouts.app title="Edit Book">   
    <h1 class="mb-4 text-2xl font-bold">Edit Book</h1>

    <div class="max-w-xl rounded bg-white p-6 shadow">
        <form method="POST" action="{{ route('books.update', $book->id) }}">
            @csrf
            @method('PUT')

            @include('books.form', ['submitLabel' => 'Update Book'])
        </form>
    </div>
</x-layouts.app>