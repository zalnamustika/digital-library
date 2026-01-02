<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;

class BookController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->query('query');

        $books = Book::query()
            ->when($query, fn($q) =>
                $q->where('title','like',"%{$query}%")
                  ->orWhere('author','like',"%{$query}%")
            )
            ->orderBy('title')
            ->paginate(10);

        return response()->json($books);
    }
}
