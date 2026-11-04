<x-layouts.app title="Edit Member">

    <h1 class="mb-4 text-2xl font-bold">Edit Member</h1>

    <div class="max-w-xl rounded bg-white p-6 shadow">
        {{-- Sends the form data to MemberController@update --}}
        <form method="POST" action="{{ route('members.update', $member) }}">
            @csrf
            {{-- HTML forms only support GET and POST, so we "pretend" to send PUT --}}
            @method('PUT')

            {{-- Same shared fields. $member comes from the controller. --}}
            @include('members.form', ['submitLabel' => 'Update Member'])
        </form>
    </div>

</x-layouts.app>