<?php

namespace Database\Seeders;

use DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BookSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('books')->insert([
            [
                'genre_id'=>1, // Fantasy
                'title'=>'Laut Bercerita',
                'year'=>2017,
                'description'=>'A story about the struggle of an activist named Laut during the New Order era, and the emotional turmoil faced by his family.',
                'summary'=>'A tale of struggle, sacrifice, and loss during a dark chapter in Indonesian history.',
                'image'=>'images/books/Laut-Bercerita.jpg',
                'author'=>'Leila S. Chudori',
                'publisher'=>'Kepustakaan Populer Gramedia',
                'pages'=>379,
                'quota'=>3
            ],
            [
                'genre_id'=>2, // Self-improvement
                'title'=>'The Psychology of Money',
                'year'=>2020,
                'description'=>'A book that discusses the emotional and psychological relationship humans have with money, and how mindset and attitude can influence financial decisions.',
                'summary'=>'Explores how a healthy mindset about money can help achieve financial freedom.',
                'image'=>'images/books/The-Psychology-of-Money.jpg',
                'author'=>'Morgan Housel',
                'publisher'=>'Harriman House',
                'pages'=>256,
                'quota'=>2
            ],
            [
                'genre_id'=>2, // Self-improvement
                'title'=>'The Maxwell Daily Reader',
                'year'=>2008,
                'description'=>'A book of daily inspirational quotes and leadership lessons compiled by John C. Maxwell to help readers improve their personal and professional qualities every day throughout the year.',
                'summary'=>'A daily guide offering leadership lessons for personal and professional development.',
                'image'=>'images/books/The-Maxwell-Daily-Reader.jpg',
                'author'=>'John C. Maxwell',
                'publisher'=>'Thomas Nelson',
                'pages'=>432,
                'quota'=>2
            ],
            [
                'genre_id'=>4, // Comedy
                'title'=>'Home Sweet Loan',
                'year'=>2021,
                'description'=>'A romantic comedy novel that tells the struggles of young people facing the reality of life, especially in saving money to own their dream house amidst social pressures.',
                'summary'=>'A humorous story about the struggles of millennials trying to achieve their dream of owning a house amidst various obstacles.',
                'image'=>'images/books/Home-Sweet-Loan.jpg',
                'author'=>'Almira Bastari',
                'publisher'=>'Gramedia Pustaka Utama',
                'pages'=>344,
                'quota'=>5
            ],
            [
                'genre_id'=>1, // Fantasy
                'title'=>"The Alchemist's Secret",
                'year'=>2012,
                'description'=>'A mysterious and magical adventure following an alchemist in search of the elixir of immortality.',
                'summary'=>'A magical adventure in the world of alchemy filled with secrets and challenges.',
                'image'=>'images/books/The-Alchemists-Secret.jpg',
                'author'=>'Scott Mariani',
                'publisher'=>'HarperCollins',
                'pages'=>400,
                'quota'=>1
            ],
            [
                'genre_id'=>3, // Romance
                'title'=>'The Fault in Our Stars',
                'year'=>2012,
                'description'=>'A touching love story between two cancer patients who find strength in each other.',
                'summary'=>'A love story that teaches courage and acceptance of life’s realities.',
                'image'=>'images/books/The-Fault-in-Our-Stars.jpg',
                'author'=>'John Green',
                'publisher'=>'Dutton Books',
                'pages'=>313,
                'quota'=>1
            ],
            [
                'genre_id'=>4, // Comedy
                'title'=>'Koala Kumal',
                'year'=>2015,
                'description'=>'A collection of humorous short stories about unique and quirky life experiences.',
                'summary'=>'Comedy that explores everyday life with Raditya Dika\'s signature style.',
                'image'=>'images/books/Koala-Kumal.jpg',
                'author'=>'Raditya Dika',
                'publisher'=>'GagasMedia',
                'pages'=>240,
                'quota'=>2
            ],
            [
                'genre_id'=>4, // Comedy
                'title'=>'Crazy Rich Asians',
                'year'=>2013,
                'description'=>'A comedy novel depicting the lives of rich Asians full of family drama and intrigue.',
                'summary'=>'A romantic comedy about cultural differences and social pressures within a wealthy Asian family.',
                'image'=>'images/books/Crazy-Rich-Asians.jpg',
                'author'=>'Kevin Kwan',
                'publisher'=>'Doubleday',
                'pages'=>403,
                'quota'=>3
            ],
            [
                'genre_id'=>2, // Self-improvement
                'title' => 'Effortless',
                'year' => 2021,
                'description' => 'Panduan untuk mencapai lebih banyak dengan usaha yang lebih sedikit dan menyederhanakan hidup agar fokus pada hal-hal yang benar-benar penting.',
                'summary' => 'Buku pengembangan diri yang mengajarkan cara membuat hal-hal esensial dalam hidup menjadi lebih mudah dan tercapai.',
                'image' => 'images/books/Effortless.jpg',
                'author' => 'Greg McKeown',
                'publisher' => 'Crown Publishing Group',
                'pages' => 256,
                'quota'=>1
            ],
            [
                'genre_id'=>5, // Sci-Fi
                'title'=>'Supernova: Inteligensi Embun Pagi',
                'year'=>2016,
                'description'=>'A sci-fi novel about the search for life’s meaning through interconnected characters in a complex world.',
                'summary'=>'The epic conclusion of the Supernova series, blending science, spirituality, and drama.',
                'image' => 'images/books/Supernova-Inteligensi-Embun-Pagi.jpeg',
                'author'=>'Dewi Lestari',
                'publisher'=>'Bentang Pustaka',
                'pages'=>724,
                'quota'=>2
            ],            
        ]);
    }
}
