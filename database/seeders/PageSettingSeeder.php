<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class PageSettingSeeder extends Seeder
{
    public function run(): void
    {
        Setting::truncate(); 
        
        Setting::insert([
            // Global Settings
            ['key' => 'brand_name', 'value' => 'Furni', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'meta_description', 'value' => 'Furni Free Bootstrap 5 Template for Furniture and Interior Design', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'footer_description', 'value' => 'Donec facilisis quam ut purus rutrum lobortis. Donec vitae odio quis nisl dapibus malesuada.', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'footer_newsletter_title', 'value' => 'Subscribe to Newsletter', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'footer_sofa_image', 'value' => 'images/sofa.png', 'created_at' => now(), 'updated_at' => now()],

            // Home Page
            ['key' => 'home_page_title', 'value' => 'Home - Furni', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'home_hero_title', 'value' => 'Design Modern', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'home_hero_desc', 'value' => 'We create modern interiors using high-quality materials.', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'home_hero_image', 'value' => 'images/couch.png', 'created_at' => now(), 'updated_at' => now()],

            // About Us Page
            ['key' => 'about_page_title', 'value' => 'About Us - Furni', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'about_hero_title', 'value' => 'About Us', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'about_hero_desc', 'value' => 'We are a dedicated team providing modern interior design solutions to make your home comfortable and stylish.', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'about_hero_image', 'value' => 'images/couch.png', 'created_at' => now(), 'updated_at' => now()],

            // Services Page
            ['key' => 'services_page_title', 'value' => 'Services - Furni', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'services_hero_title', 'value' => 'Our Services', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'services_hero_desc', 'value' => 'Explore our wide range of professional services tailored to meet your interior and furniture design needs.', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'services_hero_image', 'value' => 'images/couch.png', 'created_at' => now(), 'updated_at' => now()],

            // Blog Page
            ['key' => 'blog_page_title', 'value' => 'Blog - Furni', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'blog_hero_title', 'value' => 'Our Blog', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'blog_hero_desc', 'value' => 'Read our latest articles, tips, and trends about interior design and modern furniture.', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'blog_hero_image', 'value' => 'images/couch.png', 'created_at' => now(), 'updated_at' => now()],

            // Contact Page
            ['key' => 'contact_page_title', 'value' => 'Contact Us - Furni', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'contact_hero_title', 'value' => 'Contact Us', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'contact_hero_desc', 'value' => 'Have questions or want to work with us? Feel free to reach out using the form below.', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'contact_hero_image', 'value' => 'images/couch.png', 'created_at' => now(), 'updated_at' => now()],
            
            // Shop Page
            ['key' => 'shop_page_title', 'value' => 'Shop - Furni Free Bootstrap 5 Template for Furniture and Interior Design Websites', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'shop_hero_title', 'value' => 'Shop', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}