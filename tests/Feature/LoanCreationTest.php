<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Category;
use App\Models\Loan;
use App\Models\Member;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoanCreationTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_librarian_can_create_a_loan(): void
    {
        $this->actingAs(User::factory()->create());
        $category = Category::create(['name' => 'Fiction']);
        $book = Book::create([
            'category_id' => $category->id,
            'title' => 'The Library Book',
            'author' => 'A. Writer',
            'isbn' => '9780000000001',
            'total_copies' => 2,
            'available_copies' => 2,
        ]);
        $member = Member::create([
            'member_code' => 'M-001',
            'full_name' => 'Alex Reader',
            'email' => 'alex@example.com',
            'is_active' => true,
        ]);

        $response = $this->post(route('loans.store'), [
            'book_id' => $book->id,
            'member_id' => $member->id,
            'due_date' => today()->addDays(14)->toDateString(),
        ]);

        $response->assertRedirect(route('loans.index'));
        $loan = Loan::query()->where('book_id', $book->id)->firstOrFail();
        $this->assertSame($member->id, $loan->member_id);
        $this->assertSame(today()->toDateString(), $loan->borrowed_date->toDateString());
        $this->assertSame(today()->addDays(14)->toDateString(), $loan->due_date->toDateString());
        $this->assertSame('1.75', $loan->loan_fee);
        $this->assertDatabaseHas('books', ['id' => $book->id, 'available_copies' => 1]);
    }
}
