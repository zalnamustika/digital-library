<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Loan;
use Illuminate\Http\Request;

class LoanController extends Controller
{
    public function borrow(Request $request)
    {
        $request->validate(['book_id' => ['required','exists:books,id']]);

        $book = Book::findOrFail($request->book_id);
        if ($book->stock < 1) return response()->json(['message'=>'Stok habis'], 422);

        $loan = Loan::create([
            'user_id' => $request->user()->id,
            'book_id' => $book->id,
            'borrowed_at' => now(),
            'due_at' => now()->addDays(7),
            'status' => 'borrowed',
        ]);

        $book->decrement('stock');

        return response()->json($loan, 201);
    }

    public function returnLoan(Request $request, $id)
    {
        $loan = Loan::with('book')->findOrFail($id);

        if ($loan->user_id !== $request->user()->id) {
            return response()->json(['message'=>'Tidak berhak'], 403);
        }
        if ($loan->returned_at) {
            return response()->json(['message'=>'Sudah dikembalikan'], 422);
        }

        $loan->update(['returned_at'=>now(),'status'=>'returned']);
        $loan->book->increment('stock');

        return response()->json(['message'=>'Berhasil dikembalikan']);
    }
}
