<?php

namespace App\Services;

use App\Models\Book;
use Illuminate\Support\Facades\DB;

class BookService
{
    public function search(?string $query, ?int $userId = null)
    {
        if ($userId && $query && trim($query) !== '') {
            DB::table('search_histories')->insert([
                'user_id' => $userId,
                'query' => mb_substr(trim($query), 0, 100),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return Book::query()
            ->when($query, function ($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                  ->orWhere('author', 'like', "%{$query}%");
            })
            ->orderBy('title')
            ->paginate(10);
    }
}
