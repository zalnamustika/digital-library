<?php

namespace Database\Seeders;

use App\Models\Book;
use Illuminate\Database\Seeder;

class BookSeeder extends Seeder
{
    public function run(): void
    {
        Book::insert([
            ['title'=>'Clean Code','author'=>'Robert C. Martin','stock'=>3,'created_at'=>now(),'updated_at'=>now()],
            ['title'=>'Refactoring','author'=>'Martin Fowler','stock'=>2,'created_at'=>now(),'updated_at'=>now()],
            ['title'=>'Laravel Up & Running','author'=>'Matt Stauffer','stock'=>5,'created_at'=>now(),'updated_at'=>now()],
        ]);
    }
}
