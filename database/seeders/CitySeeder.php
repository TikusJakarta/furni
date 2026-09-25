<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CitySeeder extends Seeder
{
    public function run(): void
    {
        $path = public_path('data/cities.csv');

        if (file_exists($path)) {
            if (($handle = fopen($path, 'r')) !== FALSE) {
                fgetcsv($handle, 1000, ','); 

                while (($data = fgetcsv($handle, 1000, ',')) !== FALSE) {
                    if (!empty($data[0]) && !empty($data[1])) {
                        DB::table('cities')->updateOrInsert([
                            'city' => trim($data[0]),
                            'postal_code' => trim($data[1]),
                        ]);
                    }
                }
                fclose($handle);
            }
            $this->command->info("Mantap! Data kota berhasil diimpor ke database.");
        } else {
            $this->command->error("File cities.csv tidak ditemukan di public/data/!");
        }
    }
}