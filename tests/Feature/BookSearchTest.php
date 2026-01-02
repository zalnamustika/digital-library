<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class BookSearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_search_books_by_title_or_author(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        Book::factory()->create(['title' => 'Laravel Dasar', 'author' => 'Budi']);
        Book::factory()->create(['title' => 'Belajar PHP', 'author' => 'Andi']);

        $res = $this->getJson('/api/books?query=Laravel');

        $res->assertOk();
        // pagination: data ada di key "data"
        $res->assertJsonFragment(['title' => 'Laravel Dasar']);
    }
}
