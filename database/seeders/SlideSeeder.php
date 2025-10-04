<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class SlideSeeder extends Seeder {
    /**
    * Run the database seeds.
    */

    public function run(): void {
        $slides = [
            [
                'id' => 1,
                'tagline' => 'Winter is coming, but fire will answer',
                'title' => 'A song of ice and fire',
                'subtitle' => 'GEORGE R. MARTIN',
                'link' => 'shop/a-song-of-ice-and-fire',
                'image' => '1759419436.jpg',
                'status' => 1,
                'created_at' => Carbon::parse( '2025-09-28 07:02:28' ),
                'updated_at' => Carbon::parse( '2025-10-02 08:37:32' ),
            ],
            [
                'id' => 2,
                'tagline' => 'In the desert lies destiny.',
                'title' => 'Dune',
                'subtitle' => 'Frank Herbert',
                'link' => 'shop/dune',
                'image' => '1759419583.jpg',
                'status' => 1,
                'created_at' => Carbon::parse( '2025-10-02 08:39:43' ),
                'updated_at' => Carbon::parse( '2025-10-02 08:39:43' ),
            ],
            [
                'id' => 3,
                'tagline' => 'Big Brother is always watching.',
                'title' => '1984',
                'subtitle' => 'GEORGE ORWELL',
                'link' => 'shop/1984',
                'image' => '1759419671.jpg',
                'status' => 1,
                'created_at' => Carbon::parse( '2025-10-02 08:41:11' ),
                'updated_at' => Carbon::parse( '2025-10-02 10:01:42' ),
            ],
        ];

        DB::table( 'slides' )->insert( $slides );
    }
}
