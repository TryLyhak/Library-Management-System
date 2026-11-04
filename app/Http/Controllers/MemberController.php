<?php

namespace App\Http\Controllers;

use App\Models\Member;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class MemberController extends Controller
{
    // Show the list of members, with optional search
    public function index(Request $request): View
    {
        $search = $request->string('search')->trim()->toString();

        $members = Member::query()
            // Count only loans that are not returned yet (returned_date is empty)
            ->withCount(['loans as active_loans_count' => fn ($query) => $query->whereNull('returned_date')])
            ->when($search, function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('full_name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('member_code', 'like', "%{$search}%");
                });
            })
            ->orderBy('id')
            ->paginate(10)
            ->withQueryString(); 

        return view('members.index', compact('members', 'search'));
    }

    // Show the empty "add member" form
    public function create(): View
    {
        return view('members.create');
    }

    // Validate the form and save a new member
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:members,email'],
            'phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:255'],
        ]);

     
        $validated['member_code'] = uniqid('TEMP-');
        $validated['is_active'] = $request->boolean('is_active');

        $member = Member::create($validated);

        // Build the real code from the new id: id 2 becomes "LIB-0002"
        $member->update([
            'member_code' => 'LIB-'.str_pad((string) $member->id, 4, '0', STR_PAD_LEFT),
        ]);

        return redirect()
            ->route('members.index')
            ->with('success', "Member {$member->member_code} created successfully.");
    }

    // Show the "edit member" form
    public function edit(Member $member): View
    {
        return view('members.edit', compact('member'));
    }

    // Validate the form and update the member
    public function update(Request $request, Member $member): RedirectResponse
    {
        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            // ignore() lets the member keep their own email without a "duplicate" error
            'email' => ['required', 'email', 'max:255', Rule::unique('members', 'email')->ignore($member->id)],
            'phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:255'],
        ]);

        // An unchecked checkbox is not sent by the browser, so boolean() returns false
        $validated['is_active'] = $request->boolean('is_active');

        $member->update($validated);

        return redirect()
            ->route('members.index')
            ->with('success', 'Member updated successfully.');
    }

    // Delete a member (only if they have no loan history)
    public function destroy(Member $member): RedirectResponse
    {
        if ($member->loans()->exists()) {
            return back()->with('error', 'Cannot delete a member who has loan records. Deactivate them instead.');
        }

        $member->delete();

        return redirect()
            ->route('members.index')
            ->with('success', 'Member deleted successfully.');
    }
}
