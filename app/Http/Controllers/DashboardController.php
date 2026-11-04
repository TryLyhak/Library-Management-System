<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Loan;
use App\Models\Member;
use Illuminate\View\View;

class DashboardController extends Controller
{
   
    public function __invoke(): View
    {
        $stats = [
            'book_titles' => Book::count(),
            'total_copies' => (int) Book::sum('total_copies'),
            'available_copies' => (int) Book::sum('available_copies'),
            'active_members' => Member::where('is_active', true)->count(),
            // A loan with no returned_date is still borrowed
            'borrowed_now' => Loan::whereNull('returned_date')->count(),
            'overdue' => Loan::whereNull('returned_date')
                ->whereDate('due_date', '<', today())
                ->count(),
        ];

        // The 5 newest loans, with their book and member loaded in advance
        $recentLoans = Loan::with(['book', 'member'])
            ->latest('id')
            ->take(5)
            ->get();

        return view('dashboard', compact('stats', 'recentLoans'));
    }
}
