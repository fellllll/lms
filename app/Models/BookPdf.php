<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookPdf extends Model
{
    protected $table = 'book_pdf';
    protected $fillable = ['book_id', 'pdf'];

    public function book()
    {
        return $this->belongsTo(Book::class);
    }
}