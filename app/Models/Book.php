<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    //
    protected $fillable = [
        'title',
        'year',
        'description',
        'summary',
        'author',
        'publisher',
        'pages',
        'quota',
        'image',
        'genre_id'
    ];

    public function genre()
    {
        return $this->belongsTo(Genre::class);
    }

    public function reserves()
    {
        return $this->hasMany(Reserve::class);
    }

    public function users()
    {
        return $this->belongsToMany(Book::class, 'reserve', 'book_id', 'user_id');
    }
}
