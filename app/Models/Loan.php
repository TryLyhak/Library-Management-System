<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    //
    protected $fillable = [
        'book_id',
        'member_id',
        'borrowed_date',
        'due_date',
        'loan_fee',
        'returned_date',
        'fine_amount',
    ];

    protected $casts = [
        'borrowed_date' => 'date',
        'due_date' => 'date',
        'loan_fee' => 'decimal:2',
        'returned_date' => 'date',
        'fine_amount' => 'decimal:2',
    ];

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    public function member()
    {
        return $this->belongsTo(Member::class);
    }
}
