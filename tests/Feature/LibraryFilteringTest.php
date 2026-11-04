<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Category;
use App\Models\Loan;
use App\Models\Member;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LibraryFilteringTest extends TestCase
{
    use RefreshDatabase;

    public function test_categories_can_be_searched_by_name_or_description(): void
    {
        $this->actingAs(User::factory()->create());
        Category::create(['name' => 'Fiction', 'description' => 'Stories and novels']);
        Category::create(['name' => 'Science', 'description' => 'Research and discovery']);

        $response = $this->get(route('categories.index', ['search' => 'novels']));

        $response->assertOk();
        $response->assertSee('Fiction');
        $response->assertDontSee('Science');
    }

    public function test_loans_can_be_searched_by_book_or_member(): void
    {
        $this->actingAs(User::factory()->create());
        $category = Category::create(['name' => 'Fiction']);
        $book = Book::create([
            'category_id' => $category->id,
            'title' => 'The Quiet Library',
            'author' => 'A. Writer',
            'isbn' => '9780000000002',
            'total_copies' => 1,
            'available_copies' => 0,
        ]);
        $member = Member::create([
            'member_code' => 'M-002',
            'full_name' => 'Jamie Reader',
            'email' => 'jamie@example.com',
            'is_active' => true,
        ]);
        Loan::create([
            'book_id' => $book->id,
            'member_id' => $member->id,
            'borrowed_date' => today(),
            'due_date' => today()->addDays(14),
        ]);

        $response = $this->get(route('loans.index', ['search' => 'Jamie']));

        $response->assertOk();
        $response->assertSee('The Quiet Library');
        $response->assertSee('Jamie Reader');
    }
}
