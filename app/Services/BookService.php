<?php

namespace App\Services;

use App\Models\Book;
use Illuminate\Support\Facades\DB;

class BookService
{
    public function search(?string $query, int $userId)
    {
        if ($query) {
            DB::table('search_histories')->insert([
                'user_id' => $userId,
                'query' => $query,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return Book::query()
            ->when($query, fn($q) =>
                $q->where('title','like',"%{$query}%")
                  ->orWhere('author','like',"%{$query}%")
            )
            ->paginate(10);
    }
}
