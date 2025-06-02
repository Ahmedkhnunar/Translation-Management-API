<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tag;

class TagSeeder extends Seeder
{
    public function run(): void
    {
        $tags = [
            ['name' => 'Web', 'slug' => 'web'],
            ['name' => 'Mobile', 'slug' => 'mobile'],
            ['name' => 'Desktop', 'slug' => 'desktop'],
            ['name' => 'UI', 'slug' => 'ui'],
            ['name' => 'Auth', 'slug' => 'auth'],
        ];

        foreach ($tags as $tag) {
            Tag::firstOrCreate(['slug' => $tag['slug']], $tag);
        }
    }
}
