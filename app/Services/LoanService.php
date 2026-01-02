<?php

namespace App\Services;

use App\Models\Book;
use App\Models\Loan;
use Illuminate\Support\Facades\DB;

class LoanService
{
    public function borrow(int $userId, int $bookId): Loan
    {
        return DB::transaction(function () use ($userId, $bookId) {
            $book = Book::lockForUpdate()->findOrFail($bookId);

            if ($book->stock < 1) {
                throw new \Exception('Stok habis');
            }

            $loan = Loan::create([
                'user_id' => $userId,
                'book_id' => $bookId,
                'borrowed_at' => now(),
                'due_at' => now()->addDays(7),
                'status' => 'borrowed',
            ]);

            $book->decrement('stock');

            return $loan;
        });
    }

    public function return(int $loanId, int $userId): void
    {
        $loan = Loan::with('book')->findOrFail($loanId);

        if ($loan->user_id !== $userId) {
            throw new \Exception('Tidak berhak');
        }

        $loan->update([
            'returned_at' => now(),
            'status' => 'returned',
        ]);

        $loan->book->increment('stock');
    }
}
