<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Genre extends Model
{
    //
    use HasFactory; // Add this line if it's missing

    public function books()
    {
        return $this->hasMany(Book::class);
    }
}
