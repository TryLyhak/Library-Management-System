<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Loan;
use App\Models\Member;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class LoanController extends Controller
{
    // Library rules. Change them here if the library's rules change.
    private const MAX_ACTIVE_LOANS = 3;   // books one member can hold at once

    private const FREE_LOAN_DAYS = 7;     // first week is free

    private const EXTENSION_FEE_PER_DAY = 0.25; // fee for days beyond the first week

    private const LATE_FINE_PER_DAY = 0.50; // late-return fine per day

    // Show the list of loans, with an optional status filter
    public function index(Request $request): View
    {
        $status = $request->string('status')->toString(); // active, overdue, returned or empty
        $search = $request->string('search')->trim()->toString();

        $loans = Loan::with(['book', 'member']) // eager loading avoids extra queries
            ->when($search, function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->whereHas('book', function ($query) use ($search) {
                        $query->where('title', 'like', "%{$search}%")
                            ->orWhere('author', 'like', "%{$search}%")
                            ->orWhere('isbn', 'like', "%{$search}%");
                    })->orWhereHas('member', function ($query) use ($search) {
                        $query->where('full_name', 'like', "%{$search}%")
                            ->orWhere('member_code', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
                });
            })
            ->when($status === 'active', fn ($query) => $query->whereNull('returned_date'))
            ->when($status === 'overdue', fn ($query) => $query
                ->whereNull('returned_date')
                ->whereDate('due_date', '<', today()))
            ->when($status === 'returned', fn ($query) => $query->whereNotNull('returned_date'))
            ->orderByDesc('id') // newest first
            ->paginate(10)
            ->withQueryString();

        return view('loans.index', compact('loans', 'status', 'search'));
    }

    // Show the "borrow a book" form
    public function create(): View
    {
        // Only books that are on the shelf, and only active members
        $books = Book::where('available_copies', '>', 0)->orderBy('title')->get();
        $members = Member::where('is_active', true)->orderBy('full_name')->get();

        return view('loans.create', compact('books', 'members'));
    }

    // Validate the form and create the loan
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'book_id' => ['required', 'exists:books,id'],
            'member_id' => ['required', 'exists:members,id'],
            'due_date' => ['required', 'date', 'after_or_equal:today'],
        ]);

      
        $error = DB::transaction(function () use ($validated) {

            // lockForUpdate() stops two librarians from borrowing the last copy at the same time
            $book = Book::lockForUpdate()->findOrFail($validated['book_id']);
            $member = Member::findOrFail($validated['member_id']);

            if (! $member->is_active) {
                return 'This member is inactive and cannot borrow books.';
            }

            if ($book->available_copies < 1) {
                return 'No copies of this book are available right now.';
            }

            $activeLoansCount = $member->loans()->whereNull('returned_date')->count();
            if ($activeLoansCount >= self::MAX_ACTIVE_LOANS) {
                return 'This member already has '.self::MAX_ACTIVE_LOANS.' unreturned books.';
            }

            $alreadyHasBook = $member->loans()
                ->whereNull('returned_date')
                ->where('book_id', $book->id)
                ->exists();
            if ($alreadyHasBook) {
                return 'This member is already borrowing this book.';
            }

            $durationDays = today()->diffInDays($validated['due_date']);
            $loanFee = max(0, $durationDays - self::FREE_LOAN_DAYS) * self::EXTENSION_FEE_PER_DAY;

            // All rules passed: save the loan and lower the available copies
            Loan::create([
                'book_id' => $book->id,
                'member_id' => $member->id,
                'borrowed_date' => today(),
                'due_date' => today()->addDays($durationDays),
                'loan_fee' => $loanFee,
            ]);
            $book->decrement('available_copies');

            return null; // null means "no error"
        });

        if ($error) {
            // Go back to the form, keep what the user chose, and show the problem
            return back()->withInput()->with('error', $error);
        }

        return redirect()
            ->route('loans.index')
            ->with('success', 'Book borrowed successfully.');
    }

    public function returnBook(Loan $loan): RedirectResponse
    {
        $fine = DB::transaction(function () use ($loan) {

            // Reload the loan with a lock, in case two people click "Return" together
            $loan = Loan::lockForUpdate()->findOrFail($loan->id);

            if ($loan->returned_date !== null) {
                return null; // already returned
            }

            // Fine = days late x fine per day (0 if returned on time)
            $fine = 0;
            if (today()->greaterThan($loan->due_date)) {
                $daysLate = (int) $loan->due_date->diffInDays(today());
                $fine = $daysLate * self::LATE_FINE_PER_DAY;
            }

            $loan->update([
                'returned_date' => today(),
                'fine_amount' => $fine,
            ]);

            // The copy is back on the shelf
            Book::whereKey($loan->book_id)->increment('available_copies');

            return $fine;
        });

        if ($fine === null) {
            return back()->with('error', 'This loan was already returned.');
        }

        $message = $fine > 0
            ? sprintf('Book returned. Late fine: $%.2f.', $fine)
            : 'Book returned on time.';

        return back()->with('success', $message);
    }
}
