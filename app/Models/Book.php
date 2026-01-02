<?php

namespace App\Models;

use App\Domains\Loan\Models\Loan;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'author', 'isbn', 'published_year', 'stock',
    ];

    public function loans()
    {
        return $this->hasMany(Loan::class);
    }
}
