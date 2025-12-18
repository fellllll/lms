<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bookmark extends Model
{
    protected $fillable = ['user_id', 'book_pdf_id', 'page_number'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function bookPdf()
    {
        return $this->belongsTo(BookPdf::class);
    }
}
