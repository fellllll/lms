<?php

namespace App\Services;

use App\Models\Book;

class BookService {
    public function searchBooks($books, $searchTerm)
    {
        if ($searchTerm) {
            return array_filter($books, function ($book) use ($searchTerm) {
                return stripos($book['title'], $searchTerm) !== false || 
                stripos($book['author'], $searchTerm) !== false;
            });
        }
        return $books;
    }

}
