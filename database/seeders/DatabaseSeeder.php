<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;
use App\Models\Product;
use App\Models\Feature;
use App\Models\Testimonial;
use App\Models\BlogPost;
use App\Models\Team; // <-- Jangan lupa import model Team

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Pengaturan Website
        Setting::insert([
            ['key' => 'hero_title_1', 'value' => 'Modern Interior'],
            ['key' => 'hero_title_2', 'value' => 'Design Studio'],
            ['key' => 'hero_desc', 'value' => 'Donec vitae odio quis nisl dapibus malesuada. Nullam ac aliquet velit. Aliquam vulputate velit imperdiet dolor tempor tristique.'],
            ['key' => 'about_us_desc', 'value' => 'Donec facilisis quam ut purus rutrum lobortis. Donec vitae odio quis nisl dapibus malesuada. Nullam ac aliquet velit...'],
            ['key' => 'about_hero_desc', 'value' => 'Donec vitae odio quis nisl dapibus malesuada. Nullam ac aliquet velit. Aliquam vulputate velit imperdiet dolor tempor tristique.'],
        ]);

        // Produk Utama (3 Produk Section Pertama di Home)
        Product::insert([
            ['name' => 'Nordic Chair', 'price' => 50.00, 'image' => 'images/product-1.png', 'description' => 'Crafted with excellent material.', 'type' => 'main'],
            ['name' => 'Kruzo Aero Chair', 'price' => 78.00, 'image' => 'images/product-2.png', 'description' => 'Crafted with excellent material.', 'type' => 'main'],
            ['name' => 'Ergonomic Chair', 'price' => 43.00, 'image' => 'images/product-3.png', 'description' => 'Crafted with excellent material.', 'type' => 'main'],
            
            // Produk Samping Kanan (Popular Products)
            ['name' => 'Nordic Chair', 'price' => 50.00, 'image' => 'images/product-1.png', 'description' => 'Donec facilisis quam ut purus rutrum lobortis. Donec vitae odio', 'type' => 'popular'],
            ['name' => 'Kruzo Aero Chair', 'price' => 78.00, 'image' => 'images/product-2.png', 'description' => 'Donec facilisis quam ut purus rutrum lobortis. Donec vitae odio', 'type' => 'popular'],
            ['name' => 'Ergonomic Chair', 'price' => 43.00, 'image' => 'images/product-3.png', 'description' => 'Donec facilisis quam ut purus rutrum lobortis. Donec vitae odio', 'type' => 'popular'],
        ]);

        // Fitur Why Choose Us
        Feature::insert([
            ['icon' => 'images/truck.svg', 'title' => 'Fast & Free Shipping', 'description' => 'Donec vitae odio quis nisl dapibus malesuada. Nullam ac aliquet velit.'],
            ['icon' => 'images/bag.svg', 'title' => 'Easy to Shop', 'description' => 'Donec vitae odio quis nisl dapibus malesuada. Nullam ac aliquet velit.'],
            ['icon' => 'images/support.svg', 'title' => '24/7 Support', 'description' => 'Donec vitae odio quis nisl dapibus malesuada. Nullam ac aliquet velit.'],
            ['icon' => 'images/return.svg', 'title' => 'Hassle Free Returns', 'description' => 'Donec vitae odio quis nisl dapibus malesuada. Nullam ac aliquet velit.'],
        ]);

        // Testimoni
        Testimonial::insert([
            [
                'quote' => 'Donec facilisis quam ut purus rutrum lobortis. Donec vitae odio quis nisl dapibus malesuada. Nullam ac aliquet velit. Aliquam vulputate velit imperdiet dolor tempor tristique.',
                'name' => 'Maria Jones',
                'position' => 'CEO, Co-Founder, XYZ Inc.',
                'image' => 'images/person-1.png'
            ],
            [
                'quote' => 'Facilisis quam ut purus rutrum lobortis. Donec vitae odio quis nisl dapibus malesuada. Nullam ac aliquet velit.',
                'name' => 'Ahmad Fauzi',
                'position' => 'Interior Designer',
                'image' => 'images/person-1.png' 
            ],
            [
                'quote' => 'Aliquam vulputate velit imperdiet dolor tempor tristique. Pellentesque habitant morbi tristique senectus.',
                'name' => 'Sarah Jenkins',
                'position' => 'Home Owner',
                'image' => 'images/person-1.png'
            ]
        ]);

        // Data Team (Untuk Halaman About)
        // Data Team (Untuk Halaman About)
        Team::insert([
            [
                'first_name' => 'Lawson',
                'last_name' => 'Arnold',
                'position' => 'CEO, Founder, Atty.',
                'bio' => 'Separated they live in. Separated they live in Bookmarksgrove right at the coast of the Semantics.',
                'image' => 'images/person_1.jpg'
            ],
            [
                'first_name' => 'Jeremy',
                'last_name' => 'Walker',
                'position' => 'Mastermind, Co-Founder',
                'bio' => 'Separated they live in. Separated they live in Bookmarksgrove right at the coast of the Semantics.',
                'image' => 'images/person_2.jpg'
            ],
            [
                'first_name' => 'Patrik',
                'last_name' => 'White',
                'position' => 'Relocation Marketing',
                'bio' => 'Separated they live in. Separated they live in Bookmarksgrove right at the coast of the Semantics.',
                'image' => 'images/person_3.jpg'
            ],
            [
                'first_name' => 'Kathryn',
                'last_name' => 'Ryan',
                'position' => 'Repo, New Yorker',
                'bio' => 'Separated they live in. Separated they live in Bookmarksgrove right at the coast of the Semantics.',
                'image' => 'images/person_4.jpg'
            ],
        ]);

        // Blog
        BlogPost::insert([
            ['title' => 'First Time Home Owner Ideas', 'slug' => 'first-time-home-owner-ideas', 'image' => 'images/post-1.jpg', 'author' => 'Kristin Watson', 'date' => '2021-12-19'],
            ['title' => 'How To Keep Your Furniture Clean', 'slug' => 'how-to-keep-your-furniture-clean', 'image' => 'images/post-2.jpg', 'author' => 'Robert Fox', 'date' => '2021-12-15'],
            ['title' => 'Small Space Furniture Apartment Ideas', 'slug' => 'small-space-furniture-apartment-ideas', 'image' => 'images/post-3.jpg', 'author' => 'Kristin Watson', 'date' => '2021-12-12'],
        ]);

        $this->call([
        PageSettingSeeder::class,
    ]);
    }
}