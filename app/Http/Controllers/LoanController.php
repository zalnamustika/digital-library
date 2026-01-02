<?php

namespace App\Http\Controllers;

use App\Services\LoanService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LoanController extends Controller
{
    public function __construct(private LoanService $loanService) {}

    public function borrow(Request $request): JsonResponse
    {
        $request->validate([
            'book_id' => ['required', 'integer', 'exists:books,id'],
        ]);

        $loan = $this->loanService->borrow(
            $request->user()->id,
            (int) $request->book_id
        );

        return response()->json($loan, 201);
    }

    public function returnLoan(Request $request, int $id): JsonResponse
    {
        $this->loanService->returnLoan(
            $id,
            $request->user()->id
        );

        return response()->json(['message' => 'Berhasil dikembalikan']);
    }
}
