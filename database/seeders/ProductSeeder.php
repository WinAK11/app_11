<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder {
    /**
    * Run the database seeds.
    */

    public function run(): void {
        $products = [
            // Fiction
            [
                'name' => '1984',
                'slug' => Str::slug( '1984' ),
                'short_description' => 'A dystopian novel set in a totalitarian society.',
                'description' => '1984 by George Orwell depicts a society under constant surveillance, ruled by a totalitarian regime led by Big Brother.',
                'regular_price' => 12.99,
                'sale_price' => null,
                'SKU' => '1984-001',
                'stock_status' => 'instock',
                'featured' => 1,
                'quantity' => 100,
                'image' => null,
                'images' => null,
                'category_id' => 1, // Fiction
                'author_id' => 1, // George Orwell
            ],
            [
                'name' => 'The Great Gatsby',
                'slug' => Str::slug( 'The Great Gatsby' ),
                'short_description' => 'A novel about the American dream and the Jazz Age.',
                'description' => 'The Great Gatsby by F. Scott Fitzgerald portrays the extravagance of the 1920s and the moral decay that lies beneath the surface of the American dream.',
                'regular_price' => 10.99,
                'sale_price' => null,
                'SKU' => 'GATSBY-002',
                'stock_status' => 'instock',
                'featured' => 0,
                'quantity' => 80,
                'image' => null,
                'images' => null,
                'category_id' => 1, // Fiction
                'author_id' => 7, // F. Scott Fitzgerald
            ],

            // Non-Fiction
            [
                'name' => 'Outliers',
                'slug' => Str::slug( 'Outliers' ),
                'short_description' => 'An exploration of the factors that contribute to high levels of success.',
                'description' => 'Outliers by Malcolm Gladwell delves into what makes people successful, examining the hidden advantages and opportunities.',
                'regular_price' => 15.99,
                'sale_price' => null,
                'SKU' => 'OUTL-002',
                'stock_status' => 'instock',
                'featured' => 0,
                'quantity' => 80,
                'image' => null,
                'images' => null,
                'category_id' => 2, // Non-Fiction
                'author_id' => 2, // Malcolm Gladwell
            ],
            [
                'name' => 'Sapiens: A Brief History of Humankind',
                'slug' => Str::slug( 'Sapiens' ),
                'short_description' => 'An exploration of the history of humankind.',
                'description' => 'Sapiens by Yuval Noah Harari traces the history of human beings from the Stone Age to the modern age.',
                'regular_price' => 17.99,
                'sale_price' => null,
                'SKU' => 'SAPIENS-003',
                'stock_status' => 'instock',
                'featured' => 1,
                'quantity' => 120,
                'image' => null,
                'images' => null,
                'category_id' => 2, // Non-Fiction
                'author_id' => 8, // Yuval Noah Harari
            ],

            // Mystery
            [
                'name' => 'Murder on the Orient Express',
                'slug' => Str::slug( 'Murder on the Orient Express' ),
                'short_description' => 'A famous detective story by Agatha Christie.',
                'description' => 'Murder on the Orient Express by Agatha Christie features Hercule Poirot solving a murder on a luxury train.',
                'regular_price' => 9.99,
                'sale_price' => null,
                'SKU' => 'MURD-003',
                'stock_status' => 'instock',
                'featured' => 1,
                'quantity' => 120,
                'image' => null,
                'images' => null,
                'category_id' => 3, // Mystery
                'author_id' => 3, // Agatha Christie
            ],
            [
                'name' => 'The Girl with the Dragon Tattoo',
                'slug' => Str::slug( 'The Girl with the Dragon Tattoo' ),
                'short_description' => 'A gripping mystery involving a missing woman.',
                'description' => 'The Girl with the Dragon Tattoo by Stieg Larsson follows a journalist and a hacker as they investigate a woman who disappeared 40 years ago.',
                'regular_price' => 13.99,
                'sale_price' => null,
                'SKU' => 'GIRL-004',
                'stock_status' => 'instock',
                'featured' => 0,
                'quantity' => 150,
                'image' => null,
                'images' => null,
                'category_id' => 3, // Mystery
                'author_id' => 9, // Stieg Larsson
            ],

            // Thriller
            [
                'name' => 'Gone Girl',
                'slug' => Str::slug( 'Gone Girl' ),
                'short_description' => 'A psychological thriller about a missing wife.',
                'description' => 'Gone Girl by Gillian Flynn is a psychological thriller that explores the secrets between a married couple.',
                'regular_price' => 14.99,
                'sale_price' => null,
                'SKU' => 'GONE-004',
                'stock_status' => 'instock',
                'featured' => 0,
                'quantity' => 60,
                'image' => null,
                'images' => null,
                'category_id' => 4, // Thriller
                'author_id' => 4, // Gillian Flynn
            ],
            [
                'name' => 'The Girl on the Train',
                'slug' => Str::slug( 'The Girl on the Train' ),
                'short_description' => 'A psychological thriller about a woman who becomes entangled in a crime.',
                'description' => 'The Girl on the Train by Paula Hawkins is a mystery about a woman who becomes involved in a missing person case.',
                'regular_price' => 12.99,
                'sale_price' => null,
                'SKU' => 'TRAIN-005',
                'stock_status' => 'instock',
                'featured' => 1,
                'quantity' => 200,
                'image' => null,
                'images' => null,
                'category_id' => 4, // Thriller
                'author_id' => 10, // Paula Hawkins
            ],

            // Romance
            [
                'name' => 'The Wedding',
                'slug' => Str::slug( 'The Wedding' ),
                'short_description' => 'A love story with twists and turns.',
                'description' => 'The Wedding by Nora Roberts follows a couple through challenges and triumphs in their relationship.',
                'regular_price' => 11.99,
                'sale_price' => null,
                'SKU' => 'WEDD-005',
                'stock_status' => 'instock',
                'featured' => 1,
                'quantity' => 100,
                'image' => null,
                'images' => null,
                'category_id' => 5, // Romance
                'author_id' => 5, // Nora Roberts
            ],
            [
                'name' => 'Pride and Prejudice',
                'slug' => Str::slug( 'Pride and Prejudice' ),
                'short_description' => 'A classic romantic novel by Jane Austen.',
                'description' => 'Pride and Prejudice by Jane Austen is a romantic novel that deals with love, class, and society in early 19th-century England.',
                'regular_price' => 9.99,
                'sale_price' => null,
                'SKU' => 'PRIDE-006',
                'stock_status' => 'instock',
                'featured' => 0,
                'quantity' => 75,
                'image' => null,
                'images' => null,
                'category_id' => 5, // Romance
                'author_id' => 11, // Jane Austen
            ],

            // Science Fiction
            [
                'name' => 'I, Robot',
                'slug' => Str::slug( 'I, Robot' ),
                'short_description' => 'A collection of science fiction stories.',
                'description' => 'I, Robot by Isaac Asimov is a collection of stories that set the foundation for modern robotics.',
                'regular_price' => 13.99,
                'sale_price' => null,
                'SKU' => 'IROB-006',
                'stock_status' => 'instock',
                'featured' => 0,
                'quantity' => 85,
                'image' => null,
                'images' => null,
                'category_id' => 6, // Science Fiction
                'author_id' => 6, // Isaac Asimov
            ],
            [
                'name' => 'Dune',
                'slug' => Str::slug( 'Dune' ),
                'short_description' => 'A groundbreaking science fiction novel set on a desert planet.',
                'description' => 'Dune by Frank Herbert is a monumental science fiction novel set on the desert planet Arrakis, where politics, religion, and ecology intertwine.',
                'regular_price' => 16.99,
                'sale_price' => null,
                'SKU' => 'DUNE-007',
                'stock_status' => 'instock',
                'featured' => 1,
                'quantity' => 110,
                'image' => null,
                'images' => null,
                'category_id' => 6, // Science Fiction
                'author_id' => 12, // Frank Herbert
            ],

            // Fantasy
            [
                'name' => 'The Hobbit',
                'slug' => Str::slug( 'The Hobbit' ),
                'short_description' => 'A fantasy novel by J.R.R. Tolkien.',
                'description' => 'The Hobbit by J.R.R. Tolkien is a high-fantasy novel that follows Bilbo Baggins on his journey to reclaim a treasure.',
                'regular_price' => 12.49,
                'sale_price' => null,
                'SKU' => 'HOBBIT-008',
                'stock_status' => 'instock',
                'featured' => 1,
                'quantity' => 130,
                'image' => null,
                'images' => null,
                'category_id' => 7, // Fantasy
                'author_id' => 13, // J.R.R. Tolkien
            ],
            [
                'name' => 'A Song of Ice and Fire',
                'slug' => Str::slug( 'A Song of Ice and Fire' ),
                'short_description' => 'A fantasy series about power, betrayal, and war.',
                'description' => 'A Song of Ice and Fire by George R.R. Martin is a complex, multi-layered fantasy series set in the fictional world of Westeros.',
                'regular_price' => 19.99,
                'sale_price' => null,
                'SKU' => 'SONG-009',
                'stock_status' => 'instock',
                'featured' => 0,
                'quantity' => 95,
                'image' => null,
                'images' => null,
                'category_id' => 7, // Fantasy
                'author_id' => 1, // George R.R. Martin
            ],

            // Other categories can be added here similarly...

        ];

        DB::table( 'products' )->insert( $products );
    }
}
