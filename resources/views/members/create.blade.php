<x-layouts.app title="Add Member">

    <h1 class="mb-4 text-2xl font-bold">Add Member</h1>

    <div class="max-w-xl rounded bg-white p-6 shadow">
        {{-- Sends the form data to MemberController@store --}}
        <form method="POST" action="{{ route('members.store') }}">
            @csrf

            @include('members.form', ['submitLabel' => 'Add Member'])
        </form>
    </div>

</x-layouts.app>