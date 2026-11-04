<x-layouts.app title="Edit Category">

    <h1 class="mb-4 text-2xl font-bold">Edit Category</h1>

    <div class="max-w-xl rounded bg-white p-6 shadow">
        {{-- Sends the form data to CategoryController@update --}}
        <form method="POST" action="{{ route('categories.update', $category) }}">
            @csrf
            {{-- HTML forms only support GET and POST, so we "pretend" to send PUT --}}
            @method('PUT')

            {{-- Same shared fields. $category is already available on this page. --}}
            @include('categories.form', ['submitLabel' => 'Update Category'])
        </form>
    </div>

</x-layouts.app>
