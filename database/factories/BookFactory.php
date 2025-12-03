<?php

namespace Database\Factories;

use App\Models\Book;
use App\Models\Genre;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Book>
 */
class BookFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Book::class;

    public function definition()
    {
        return [
            'title' => $this->faker->unique()->sentence,
            'genre_id' => Genre::factory(),
            'year' => $this->faker->year,
            'author' => $this->faker->name,
            'publisher' => $this->faker->company,
            'pages' => $this->faker->numberBetween(100, 1000),
            'quota' => $this->faker->numberBetween(1, 50),
            'description' => $this->faker->paragraph,
            'summary' => $this->faker->paragraph,
            'image' => $this->faker->imageUrl(640, 480, 'books', true),
        ];
    }
}
