{{-- Title --}}
<div class="mb-4">
    <label for="title" class="mb-1 block font-medium">Title</label>
    <input type="text" id="title" name="title"
           value="{{ old('title', $book->title ?? '') }}"
           class="field">
    @error('title')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>

{{-- Author --}}
<div class="mb-4">
    <label for="author" class="mb-1 block font-medium">Author</label>
    <input type="text" id="author" name="author"
           value="{{ old('author', $book->author ?? '') }}"
           class="field">
    @error('author')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>

{{-- ISBN --}}
<div class="mb-4">
    <label for="isbn" class="mb-1 block font-medium">ISBN</label>
    <input type="text" id="isbn" name="isbn"
           value="{{ old('isbn', $book->isbn ?? '') }}"
           class="field">
    @error('isbn')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>

{{-- Category dropdown ($categories is sent from the controller) --}}
<div class="mb-4">
    <label for="category_id" class="mb-1 block font-medium">Category</label>
    <select id="category_id" name="category_id"
            class="field">
        <option value="">-- Choose a category --</option>
        @foreach ($categories as $category)
            {{-- Select the user's previous choice, or the saved category when editing --}}
            <option value="{{ $category->id }}"
                    @selected((int) old('category_id', $book->category_id ?? 0) === $category->id)>
                {{ $category->name }}
            </option>
        @endforeach
    </select>
    @error('category_id')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>

{{-- Published year and total copies, side by side on larger screens --}}
<div class="mb-4 grid gap-4 sm:grid-cols-2">
    <div>
        <label for="published_year" class="mb-1 block font-medium">Published year (optional)</label>
        <input type="number" id="published_year" name="published_year"
               value="{{ old('published_year', $book->published_year ?? '') }}"
               class="field">
        @error('published_year')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="total_copies" class="mb-1 block font-medium">Total copies</label>
        {{-- Defaults to 1 on the create page --}}
        <input type="number" id="total_copies" name="total_copies" min="1"
               value="{{ old('total_copies', $book->total_copies ?? 1) }}"
               class="field">
        @error('total_copies')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>
</div>

<div class="flex items-center gap-3">
    <button type="submit" class="btn-primary">
        {{ $submitLabel ?? 'Save' }}
    </button>
    <a href="{{ route('books.index') }}" class="btn-secondary">Cancel</a>
</div>