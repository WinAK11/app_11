<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class AuthorSeeder extends Seeder {
    /**
    * Run the database seeds.
    */

    public function run(): void {
        $authors = [
            // Fiction
            [ 'name' => 'George Orwell', 'slug' => Str::slug( 'George Orwell' ), 'nationality' => 'British', 'biography' => 'George Orwell was an English novelist, essayist, journalist, and critic.', 'image' => null ],

            // Non-Fiction
            [ 'name' => 'Malcolm Gladwell', 'slug' => Str::slug( 'Malcolm Gladwell' ), 'nationality' => 'Canadian', 'biography' => 'Malcolm Gladwell is a journalist, author, and speaker.', 'image' => null ],

            // Mystery
            [ 'name' => 'Agatha Christie', 'slug' => Str::slug( 'Agatha Christie' ), 'nationality' => 'British', 'biography' => 'Agatha Christie was an English writer known for her detective novels.', 'image' => null ],

            // Thriller
            [ 'name' => 'Gillian Flynn', 'slug' => Str::slug( 'Gillian Flynn' ), 'nationality' => 'American', 'biography' => 'Gillian Flynn is an American novelist and screenwriter.', 'image' => null ],

            // Romance
            [ 'name' => 'Nora Roberts', 'slug' => Str::slug( 'Nora Roberts' ), 'nationality' => 'American', 'biography' => 'Nora Roberts is a bestselling American author of romance novels.', 'image' => null ],

            // Science Fiction
            [ 'name' => 'Isaac Asimov', 'slug' => Str::slug( 'Isaac Asimov' ), 'nationality' => 'American', 'biography' => 'Isaac Asimov was a Russian-born American author and professor of biochemistry.', 'image' => null ],

            // Fantasy
            [ 'name' => 'J.R.R. Tolkien', 'slug' => Str::slug( 'J.R.R. Tolkien' ), 'nationality' => 'British', 'biography' => 'J.R.R. Tolkien was an English writer, best known for The Lord of the Rings.', 'image' => null ],

            // Historical Fiction
            [ 'name' => 'Ken Follett', 'slug' => Str::slug( 'Ken Follett' ), 'nationality' => 'Welsh', 'biography' => 'Ken Follett is a Welsh author known for his historical fiction novels.', 'image' => null ],

            // Biography
            [ 'name' => 'Steve Jobs', 'slug' => Str::slug( 'Steve Jobs' ), 'nationality' => 'American', 'biography' => 'Steve Jobs was an American entrepreneur and co-founder of Apple Inc.', 'image' => null ],

            // Memoir
            [ 'name' => 'Michelle Obama', 'slug' => Str::slug( 'Michelle Obama' ), 'nationality' => 'American', 'biography' => 'Michelle Obama is an American attorney, author, and former First Lady of the United States.', 'image' => null ],

            // Self-Help
            [ 'name' => 'Dale Carnegie', 'slug' => Str::slug( 'Dale Carnegie' ), 'nationality' => 'American', 'biography' => 'Dale Carnegie was an American writer and lecturer, best known for his self-help books.', 'image' => null ],

            // Health & Wellness
            [ 'name' => 'Deepak Chopra', 'slug' => Str::slug( 'Deepak Chopra' ), 'nationality' => 'Indian-American', 'biography' => 'Deepak Chopra is a prominent figure in the field of alternative medicine.', 'image' => null ],

            // Science & Nature
            [ 'name' => 'Richard Dawkins', 'slug' => Str::slug( 'Richard Dawkins' ), 'nationality' => 'British', 'biography' => 'Richard Dawkins is an evolutionary biologist and author of several books.', 'image' => null ],

            // Travel
            [ 'name' => 'Paul Theroux', 'slug' => Str::slug( 'Paul Theroux' ), 'nationality' => 'American', 'biography' => 'Paul Theroux is an American novelist and travel writer.', 'image' => null ],

            // Cooking
            [ 'name' => 'Julia Child', 'slug' => Str::slug( 'Julia Child' ), 'nationality' => 'American', 'biography' => 'Julia Child was an American chef and television personality.', 'image' => null ],

            // Art & Photography
            [ 'name' => 'Ansel Adams', 'slug' => Str::slug( 'Ansel Adams' ), 'nationality' => 'American', 'biography' => 'Ansel Adams was an American photographer and environmentalist.', 'image' => null ],

            // Children’s Books
            [ 'name' => 'Roald Dahl', 'slug' => Str::slug( 'Roald Dahl' ), 'nationality' => 'British', 'biography' => 'Roald Dahl was a British author, famous for his children’s books.', 'image' => null ],

            // Young Adult
            [ 'name' => 'John Green', 'slug' => Str::slug( 'John Green' ), 'nationality' => 'American', 'biography' => 'John Green is an American author of young adult fiction.', 'image' => null ],

            // Poetry
            [ 'name' => 'Rupi Kaur', 'slug' => Str::slug( 'Rupi Kaur' ), 'nationality' => 'Canadian', 'biography' => 'Rupi Kaur is a Canadian poet, known for her self-published poetry collections.', 'image' => null ],

            // Religion & Spirituality
            [ 'name' => 'Eckhart Tolle', 'slug' => Str::slug( 'Eckhart Tolle' ), 'nationality' => 'German', 'biography' => 'Eckhart Tolle is a spiritual teacher and author of "The Power of Now".', 'image' => null ],

            // Business & Finance
            [ 'name' => 'Warren Buffett', 'slug' => Str::slug( 'Warren Buffett' ), 'nationality' => 'American', 'biography' => 'Warren Buffett is an American business magnate and philanthropist.', 'image' => null ],

            // Education & Teaching
            [ 'name' => 'Ken Robinson', 'slug' => Str::slug( 'Ken Robinson' ), 'nationality' => 'British', 'biography' => 'Ken Robinson was an educator and author, best known for his work on creativity and education.', 'image' => null ],

            // Comics & Graphic Novels
            [ 'name' => 'Alan Moore', 'slug' => Str::slug( 'Alan Moore' ), 'nationality' => 'British', 'biography' => 'Alan Moore is an English writer best known for his graphic novels.', 'image' => null ],

            // Technology
            [ 'name' => 'Mark Zuckerberg', 'slug' => Str::slug( 'Mark Zuckerberg' ), 'nationality' => 'American', 'biography' => 'Mark Zuckerberg is the co-founder and CEO of Facebook.', 'image' => null ],
        ];

        DB::table( 'authors' )->insert( $authors );
    }
}
