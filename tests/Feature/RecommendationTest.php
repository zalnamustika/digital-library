<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class RecommendationTest extends TestCase
{
    use RefreshDatabase;

    public function test_recommendations_based_on_search_history(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        Book::factory()->create(['title' => 'Laravel Pemula', 'author' => 'A']);
        Book::factory()->create(['title' => 'Advanced Laravel', 'author' => 'B']);
        Book::factory()->create(['title' => 'Python Dasar', 'author' => 'C']);

        // simulate history user cari "Laravel"
        DB::table('search_histories')->insert([
            'user_id' => $user->id,
            'query' => 'Laravel',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $res = $this->getJson('/api/recommendations');
        $res->assertOk();

        // tergantung response kamu: bisa {data:[...]} atau langsung array
        $json = $res->json();

        $list = $json['data'] ?? $json; // aman untuk dua format
        $titles = collect($list)->pluck('title')->implode(' ');

        $this->assertStringContainsString('Laravel', $titles);
    }
}
