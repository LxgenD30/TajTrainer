<?php

namespace Database\Seeders;

use App\Models\Material;
use Illuminate\Database\Seeder;

class MaterialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Material::create([
            'title' => 'Makhraj Al-Huruf (Articulation Points)',
            'description' => 'Learn the proper articulation points of Arabic letters for correct Tajweed recitation.',
            'type' => 'document',
            'file_path' => null,
        ]);

        Material::create([
            'title' => 'Idgham Rules and Application',
            'description' => 'Understanding Idgham (merging) rules in Tajweed with practical examples.',
            'type' => 'pdf',
            'file_path' => null,
        ]);

        Material::create([
            'title' => 'Qalqalah Letters Practice',
            'description' => 'Master the echoing sound of Qalqalah letters (ق ط ب ج د) in Quranic recitation.',
            'type' => 'document',
            'file_path' => null,
        ]);

        Material::create([
            'title' => 'Mad (Prolongation) Rules',
            'description' => 'Comprehensive guide to Mad rules including Mad Asli, Mad Farʿi, and their durations.',
            'type' => 'pdf',
            'file_path' => null,
        ]);

        Material::create([
            'title' => 'Noon Saakin and Tanween Rules',
            'description' => 'Four essential rules for Noon Saakin and Tanween: Izhaar, Idgham, Iqlab, and Ikhfaa.',
            'type' => 'document',
            'file_path' => null,
        ]);
    }
}

