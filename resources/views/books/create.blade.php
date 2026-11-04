<x-layouts.app title="Add Book">

    <h1 class="mb-4 text-2xl font-bold">Add Book</h1>

    <div class="max-w-xl rounded bg-white p-6 shadow">
        {{-- Sends the form data to BookController@store --}}
        <form method="POST" action="{{ route('books.store') }}">
            @csrf

            @include('books.form', ['submitLabel' => 'Add Book'])
        </form>
    </div>

</x-layouts.app>