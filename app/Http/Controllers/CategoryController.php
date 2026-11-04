<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CategoryController extends Controller
{
    // Show the list of categories (10 per page)
    public function index(Request $request): View
    {
        $search = $request->string('search')->trim()->toString();

        $categories = Category::withCount('books')
            ->when($search, function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->orderByDesc('books_count')
            ->orderBy('name')            
            ->paginate(10)
            ->withQueryString();

        return view('categories.index', compact('categories', 'search'));
    }

    // Show the form for creating a new category
    public function create(): View
    {
        return view('categories.create');
    }

    // Validate and store a new category in the database
    public function store(Request $request): RedirectResponse
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'description' => 'nullable|string',
        ]);
        Category::create($validatedData);

        return redirect()->route('categories.index')->with('success', 'Category created successfully.');
    }

    // Show the form for editing an existing category
    public function edit(Category $category): View
    {
        return view('categories.edit', compact('category'));
    }

    // Validate and update an existing category in the database
    public function update(Request $request, Category $category): RedirectResponse
    {
        $validatedData = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('categories')->ignore($category->id)],
            'description' => 'nullable|string',
        ]);
        $category->update($validatedData);

        return redirect()->route('categories.index')->with('success', 'Category updated successfully.');
    }

    // Delete an existing category from the database
    public function destroy(Category $category): RedirectResponse
    {
        if ($category->books()->count() > 0) {
            return redirect()->route('categories.index')->with('error', 'Cannot delete category with associated books.');
        }
        $category->delete();

        return redirect()->route('categories.index')->with('success', 'Category deleted successfully.');
    }
}
