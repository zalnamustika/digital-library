<?php

namespace App\Http\Controllers;

use App\Services\LoanService;
use Illuminate\Http\Request;

class LoanController extends Controller
{
    public function __construct(private LoanService $loanService) {}

    public function borrow(Request $request)
    {
        $request->validate(['book_id'=>'required|exists:books,id']);

        return response()->json(
            $this->loanService->borrow(
                $request->user()->id,
                $request->book_id
            ),
            201
        );
    }

    public function returnLoan(Request $request, $id)
    {
        $this->loanService->return((int)$id, $request->user()->id);

        return response()->json(['message'=>'Berhasil dikembalikan']);
    }
}
