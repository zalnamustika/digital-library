<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\BookService;

class BookController extends Controller
{
    public function __construct(private BookService $bookService) {}

    public function index(Request $request)
    {
        return response()->json(
            $this->bookService->search(
                $request->query('query'),
                $request->user()->id
            )
        );
    }
}
