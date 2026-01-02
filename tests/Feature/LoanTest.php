<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Loan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class LoanTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_borrow_book_and_stock_decreases(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $book = Book::factory()->create(['stock' => 1]);

        $res = $this->postJson('/api/loans', [
            'book_id' => $book->id,
        ]);

        $res->assertStatus(201);

        $book->refresh();
        $this->assertEquals(0, $book->stock);

        $this->assertDatabaseHas('loans', [
            'user_id' => $user->id,
            'book_id' => $book->id,
            'status' => 'borrowed',
        ]);
    }

    public function test_cannot_borrow_if_stock_empty(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $book = Book::factory()->create(['stock' => 0]);

        $res = $this->postJson('/api/loans', [
            'book_id' => $book->id,
        ]);

        $res->assertStatus(422);
    }

    public function test_user_can_return_loan_and_stock_increases(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        // stok 0 agar terlihat bertambah ketika return
        $book = Book::factory()->create(['stock' => 0]);

        // ✅ loan harus milik user yang login
        $loan = Loan::create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'borrowed_at' => now(),
            'due_at' => now()->addDays(7),
            'returned_at' => null,
            'status' => 'borrowed',
        ]);

        // sanity check (boleh dihapus kalau sudah yakin)
        $this->assertEquals($user->id, $loan->user_id);

        $res = $this->postJson("/api/loans/{$loan->id}/return");
        $res->assertOk();

        $book->refresh();
        $this->assertEquals(1, $book->stock);

        $loan->refresh();
        $this->assertEquals('returned', $loan->status);
        $this->assertNotNull($loan->returned_at);
    }
}
