<?php
namespace Tests\Unit;

use App\Services\BookService;
use PHPUnit\Framework\TestCase;

class BookSearchTest extends TestCase
{
    /**
     * @var BookService
     */
    protected $bookService;

    protected function setUp(): void {
        parent::setUp();
        
        // Membuat mock dari BookService
        $this->bookService = new BookService();
    }

    public function testSearchBooksReturnsMatchingResults()
    {
        $books = [
            ['title' => 'Effortless', 'author' => 'Greg McKeown'],
            ['title' => 'Crazy Rich Asians', 'author' => 'Kevin Kwan'],
            ['title' => 'Koala Kumal', 'author' => 'Raditya Dika'],
        ];

        $result = $this->bookService->searchBooks($books, 'Rich');
        $this->assertCount(1, $result);
        $this->assertSame('Crazy Rich Asians', $result[1]['title']);
    }

    public function testSearchBooksReturnsEmptyIfNoMatch()
    {
        $books = [
            ['title' => 'Effortless', 'author' => 'Greg McKeown'],
            ['title' => 'Crazy Rich Asians', 'author' => 'Kevin Kwan'],
            ['title' => 'Koala Kumal', 'author' => 'Raditya Dika'],
        ];

        $result = $this->bookService->searchBooks($books, 'Java');
        $this->assertCount(0, $result);
    }
}
