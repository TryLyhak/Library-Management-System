<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class BookController extends Controller
{
    // Show the list of books, with optional search and category filter
    public function index(Request $request): View
    {
        $search = $request->string('search')->trim()->toString();
        $categoryId = $request->integer('category_id');

        $books = Book::with('category') // load categories in one query (avoids N+1 queries)
            // when() only adds the condition if the value is not empty
            ->when($search, function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('title', 'like', "%{$search}%")
                        ->orWhere('author', 'like', "%{$search}%")
                        ->orWhere('isbn', 'like', "%{$search}%");
                });
            })
            ->when($categoryId, fn ($query) => $query->where('category_id', $categoryId))
            ->orderBy('id')
            ->paginate(10)
            ->withQueryString(); 

        $categories = Category::orderBy('name')->get(); // for the filter dropdown

        return view('books.index', compact('books', 'categories', 'search', 'categoryId'));
    }

    // Show the empty "add book" form
    public function create(): View
    {
        $categories = Category::orderBy('name')->get();

        return view('books.create', compact('categories'));
    }

    // Validate the form and save a new book
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'title' => ['required', 'string', 'max:255'],
            'author' => ['required', 'string', 'max:255'],
            'isbn' => ['required', 'string', 'max:20', 'unique:books,isbn'],
            'published_year' => ['nullable', 'integer', 'min:1000', 'max:'.date('Y')],
            'total_copies' => ['required', 'integer', 'min:1'],
        ]);

        // A new book starts with every copy available on the shelf
        $validated['available_copies'] = $validated['total_copies'];

        Book::create($validated);

        return redirect()
            ->route('books.index')
            ->with('success', 'Book added successfully.');
    }

    // Show the "edit book" form
    public function edit(Book $book): View
    {
        $categories = Category::orderBy('name')->get();

        return view('books.edit', compact('book', 'categories'));
    }

    // Validate the form and update the book
    public function update(Request $request, Book $book): RedirectResponse
    {
        $validated = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'title' => ['required', 'string', 'max:255'],
            'author' => ['required', 'string', 'max:255'],
            'isbn' => ['required', 'string', 'max:20', Rule::unique('books', 'isbn')->ignore($book->id)],
            'published_year' => ['nullable', 'integer', 'min:1000', 'max:'.date('Y')],
            'total_copies' => ['required', 'integer', 'min:1'],
        ]);

        $copiesOnLoan = $book->total_copies - $book->available_copies;

        if ($validated['total_copies'] < $copiesOnLoan) {
            return back()
                ->withErrors(['total_copies' => "{$copiesOnLoan} copies are currently borrowed, so the total can't be lower than that."])
                ->withInput();
        }


        $validated['available_copies'] = $validated['total_copies'] - $copiesOnLoan;

        $book->update($validated);

        return redirect()
            ->route('books.index')
            ->with('success', 'Book updated successfully.');
    }

  
    public function destroy(Book $book): RedirectResponse
    {
        if ($book->loans()->exists()) {
            return back()->with('error', 'Cannot delete a book that has loan records.');
        }

        $book->delete();

        return redirect()
            ->route('books.index')
            ->with('success', 'Book deleted successfully.');
    }
}
