<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder {
    /**
    * Run the database seeds.
    */

    public function run(): void {
        $categories = [
            [ 'name' => 'Fiction', 'slug' => Str::slug( 'Fiction' ), 'image' => null, 'parent_id' => null ],
            [ 'name' => 'Non-Fiction', 'slug' => Str::slug( 'Non-Fiction' ), 'image' => null, 'parent_id' => null ],
            [ 'name' => 'Mystery', 'slug' => Str::slug( 'Mystery' ), 'image' => null, 'parent_id' => null ],
            [ 'name' => 'Thriller', 'slug' => Str::slug( 'Thriller' ), 'image' => null, 'parent_id' => null ],
            [ 'name' => 'Romance', 'slug' => Str::slug( 'Romance' ), 'image' => null, 'parent_id' => null ],
            [ 'name' => 'Science Fiction', 'slug' => Str::slug( 'Science Fiction' ), 'image' => null, 'parent_id' => null ],
            [ 'name' => 'Fantasy', 'slug' => Str::slug( 'Fantasy' ), 'image' => null, 'parent_id' => null ],
            [ 'name' => 'Historical Fiction', 'slug' => Str::slug( 'Historical Fiction' ), 'image' => null, 'parent_id' => null ],
            [ 'name' => 'Biography', 'slug' => Str::slug( 'Biography' ), 'image' => null, 'parent_id' => null ],
            [ 'name' => 'Memoir', 'slug' => Str::slug( 'Memoir' ), 'image' => null, 'parent_id' => null ],
            [ 'name' => 'Self-Help', 'slug' => Str::slug( 'Self-Help' ), 'image' => null, 'parent_id' => null ],
            [ 'name' => 'Health & Wellness', 'slug' => Str::slug( 'Health & Wellness' ), 'image' => null, 'parent_id' => null ],
            [ 'name' => 'Science & Nature', 'slug' => Str::slug( 'Science & Nature' ), 'image' => null, 'parent_id' => null ],
            [ 'name' => 'Travel', 'slug' => Str::slug( 'Travel' ), 'image' => null, 'parent_id' => null ],
            [ 'name' => 'Cooking', 'slug' => Str::slug( 'Cooking' ), 'image' => null, 'parent_id' => null ],
            [ 'name' => 'Art & Photography', 'slug' => Str::slug( 'Art & Photography' ), 'image' => null, 'parent_id' => null ],
            [ 'name' => 'Children’s Books', 'slug' => Str::slug( 'Children’s Books' ), 'image' => null, 'parent_id' => null ],
            [ 'name' => 'Young Adult', 'slug' => Str::slug( 'Young Adult' ), 'image' => null, 'parent_id' => null ],
            [ 'name' => 'Poetry', 'slug' => Str::slug( 'Poetry' ), 'image' => null, 'parent_id' => null ],
            [ 'name' => 'Religion & Spirituality', 'slug' => Str::slug( 'Religion & Spirituality' ), 'image' => null, 'parent_id' => null ],
            [ 'name' => 'Business & Finance', 'slug' => Str::slug( 'Business & Finance' ), 'image' => null, 'parent_id' => null ],
            [ 'name' => 'Education & Teaching', 'slug' => Str::slug( 'Education & Teaching' ), 'image' => null, 'parent_id' => null ],
            [ 'name' => 'Comics & Graphic Novels', 'slug' => Str::slug( 'Comics & Graphic Novels' ), 'image' => null, 'parent_id' => null ],
            [ 'name' => 'Technology', 'slug' => Str::slug( 'Technology' ), 'image' => null, 'parent_id' => null ],
        ];

        DB::table( 'categories' )->insert( $categories );
    }
}
