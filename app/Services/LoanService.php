<?php

namespace App\Services;

use App\Models\Book;
use App\Models\Loan;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class LoanService
{
    public function borrow(int $userId, int $bookId): Loan
    {
        return DB::transaction(function () use ($userId, $bookId) {
            $book = Book::lockForUpdate()->findOrFail($bookId);

            if ($book->stock < 1) {
                throw ValidationException::withMessages(['book_id' => 'Stok habis']);
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

    public function returnLoan(int $loanId, int $userId): void
    {
        DB::transaction(function () use ($loanId, $userId) {
            $loan = Loan::lockForUpdate()->with('book')->findOrFail($loanId);

            if ($loan->user_id !== $userId) {
                throw ValidationException::withMessages(['loan' => 'Tidak berhak']);
            }

            if ($loan->returned_at) {
                throw ValidationException::withMessages(['loan' => 'Sudah dikembalikan']);
            }

            $loan->update([
                'returned_at' => now(),
                'status' => 'returned',
            ]);

            $loan->book()->lockForUpdate()->increment('stock');
        });
    }
}
