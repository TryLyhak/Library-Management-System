<x-layouts.app title="Add Category">

    <h1 class="mb-4 text-2xl font-bold">Add Category</h1>

    <div class="max-w-xl rounded bg-white p-6 shadow">
        {{-- Sends the form data to CategoryController@store --}}
        <form method="POST" action="{{ route('categories.store') }}">
            @csrf

            {{-- Pulls in the shared fields, with a custom button label --}}
            @include('categories.form', ['submitLabel' => 'Create Category'])
        </form>
    </div>

</x-layouts.app>
