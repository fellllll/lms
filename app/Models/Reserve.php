<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reserve extends Model
{
    //

    protected $fillable = [
        'user_id',
        'book_id',
        'status',
        'waktu_pinjam',
        'waktu_kembali',
    ];


    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
