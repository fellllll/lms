<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Genre;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Psr\Log\NullLogger;
use Tests\TestCase;

class BookTest extends TestCase
{

    public function test_add_book_with_valid_data()
    {
        // Valid data (Equivalence Partition - valid)
        $book = Book::factory()->create();

        $data = [
            'title' => $book->title,
            'genre_id' => $book->genre_id,
            'year' => $book->year, // Valid year (e.g., 2020)
            'author' => $book->author,
            'publisher' => $book->publisher,
            'pages' => 300, // Valid page count
            'quota' => 10, // Valid quota
            'description' => $book->description,
            'summary' => $book->summary,
            'image' => $book->image,
        ];

        $response = $this->post(route('book.submit'), $data);

        // Assert status and database entry
        $response->assertStatus(302); // Redirect status
        $this->assertDatabaseHas('books', [
            'title' => $data['title'],
            'genre_id' => $data['genre_id'],
        ]);
    }

    public function test_add_book_with_invalid_data()
    {
        // Valid data (Equivalence Partition - valid)
        $book = Book::factory()->create();

        $data = [
            'title' => $book->title,
            'genre_id' => $book->genre_id,
            'year' => $book->year, // Valid year (e.g., 2020)
            'author' => $book->author,
            'publisher' => $book->publisher,
            'pages' => Null, // Valid page count
            'quota' => 10, // Valid quota
            'description' => $book->description,
            'summary' => $book->summary,
            'image' => $book->image,
        ];

        $response = $this->post(route('book.submit'), $data);

        // Assert status and database entry
        $response->assertStatus(302); // Redirect status
    }


    public function test_add_book_boundary_values()
    {
        $validData = [
            'title' => 'Laut Bercerita',
            'genre_id' => 1, // Fantasy
            'year' => 2024, // Maksimum valid year
            'author' => 'Author Name',
            'publisher' => 'Publisher Name',
            'pages' => 1, // Minimum valid pages
            'quota' => 1, // Minimum valid quota
            'description' => 'Description',
            'summary' => 'Summary of the book',
            'image' => 'images/books/BookImage.jpg',
        ];

        // Uji batas valid
        $validBoundaryCases = [
            ['year' => 1500], // Minimum valid year
            ['year' => 2024], // Maximum valid year
            ['pages' => 2],   // Minimum valid pages
            ['quota' => 1],   // Minimum valid quota
        ];

        foreach ($validBoundaryCases as $boundaryTest) {
            $data = array_merge($validData, $boundaryTest);
            $data['title'] = $data['title'] . time(); // Menambahkan timestamp untuk membuat title unik

            $response = $this->post(route('book.submit'), $data);

            // Pastikan berhasil dan data tersimpan
            $response->assertStatus(302);
        }

        // Uji batas invalid
        $invalidBoundaryCases = [
            ['year' => 1499],  // Below minimum year
            ['year' => 2025],  // Above maximum year
            ['pages' => 0],    // Below minimum pages
            ['quota' => -1],   // Below minimum quota
        ];

        foreach ($invalidBoundaryCases as $boundaryTest) {
            $data = array_merge($validData, $boundaryTest);
            $data['title'] = $data['title'] . time(); // Menambahkan timestamp untuk membuat title unik

            $response = $this->post(route('book.submit'), $data);

            // Pastikan gagal dan data tidak tersimpan
            $response->assertStatus(302);
        }
    }

    public function test_delete_book()
    {
        $book = Book::factory()->create();

        $response = $this->delete(route('book.destroy', $book->id));

        $response->assertStatus(302); // Redirect status
    }
}
