<div class="mb-4">
    <label for="name" class="mb-1 block font-medium">Name</label>


    <input type="text" id="name" name="name" value="{{ old('name', $category->name ?? '') }}" class="field">

    @error('name')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>

<div class="mb-4">
    <label for="description" class="mb-1 block font-medium">Description (optional)</label>
    <textarea id="description" name="description" rows="3" class="field">{{ old('description', $category->description ?? '') }}</textarea>

    @error('description')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>

<div class="flex items-center gap-3">
    <button type="submit" class="btn-primary">
        {{ $submitLabel ?? 'Save' }}
    </button>
    <a href="{{ route('categories.index') }}" class="btn-secondary">Cancel</a>
</div>
