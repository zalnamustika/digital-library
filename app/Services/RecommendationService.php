<?php

namespace App\Services;

use App\Models\Book;
use Illuminate\Support\Facades\DB;

class RecommendationService
{
    public function recommend(int $userId)
    {
        $keyword = DB::table('search_histories')
            ->where('user_id', $userId)
            ->select('query', DB::raw('count(*) as c'))
            ->groupBy('query')
            ->orderByDesc('c')
            ->value('query');

        return $keyword
            ? Book::where('title','like',"%{$keyword}%")
                ->orWhere('author','like',"%{$keyword}%")
                ->get()
            : Book::inRandomOrder()->limit(5)->get();
    }
}
