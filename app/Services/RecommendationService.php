<?php

namespace App\Services;

use App\Models\Book;
use Illuminate\Support\Facades\DB;

class RecommendationService
{
    public function recommendForUser(int $userId)
    {
        $topQuery = DB::table('search_histories')
            ->select('query', DB::raw('count(*) as c'))
            ->where('user_id', $userId)
            ->groupBy('query')
            ->orderByDesc('c')
            ->value('query');

        if (!$topQuery) {
            return Book::query()->inRandomOrder()->limit(10)->get();
        }

        return Book::query()
            ->where('title', 'like', "%{$topQuery}%")
            ->orWhere('author', 'like', "%{$topQuery}%")
            ->limit(10)
            ->get();
    }
}
