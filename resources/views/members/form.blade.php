{{-- Shared fields for the create and edit member forms --}}

{{-- Show the member code only on the edit page. It is generated automatically
     and can't be changed. --}}
@isset($member)
    <div class="mb-4">
        <label class="mb-1 block font-medium">Member code</label>
        <p class="font-mono text-gray-600">{{ $member->member_code }}</p>
    </div>
@endisset

{{-- Full name --}}
<div class="mb-4">
    <label for="full_name" class="mb-1 block font-medium">Full name</label>
    <input type="text" id="full_name" name="full_name"
           value="{{ old('full_name', $member->full_name ?? '') }}"
           class="field">
    @error('full_name')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>

{{-- Email --}}
<div class="mb-4">
    <label for="email" class="mb-1 block font-medium">Email</label>
    <input type="email" id="email" name="email"
           value="{{ old('email', $member->email ?? '') }}"
           class="field">
    @error('email')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>

{{-- Phone --}}
<div class="mb-4">
    <label for="phone" class="mb-1 block font-medium">Phone (optional)</label>
    <input type="text" id="phone" name="phone"
           value="{{ old('phone', $member->phone ?? '') }}"
           class="field">
    @error('phone')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>

{{-- Address --}}
<div class="mb-4">
    <label for="address" class="mb-1 block font-medium">Address (optional)</label>
    <input type="text" id="address" name="address"
           value="{{ old('address', $member->address ?? '') }}"
           class="field">
    @error('address')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>

{{-- Active checkbox --}}
<div class="mb-4">
    {{-- A hidden "0" is sent when the box is unchecked. Browsers send nothing for
         an unchecked checkbox, and this makes old('is_active') work after a
         validation error. --}}
    <input type="hidden" name="is_active" value="0">

    <label class="flex items-center gap-2">
        <input type="checkbox" name="is_active" value="1"
               {{-- Checked by default on the create page --}}
               @checked(old('is_active', $member->is_active ?? true))
               class="h-4 w-4 rounded border-slate-400 text-black focus:ring-slate-400">
        <span class="font-medium">Active member</span>
    </label>
</div>

<div class="flex flex-col-reverse gap-3 border-t border-slate-100 pt-6 sm:flex-row sm:justify-end">
    <a href="{{ route('members.index') }}" class="btn-secondary">Cancel</a>
    <button type="submit" class="btn-primary">
        {{ $submitLabel ?? 'Save Member' }}
    </button>
</div>