<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Reaction;

class ReactionSeeder extends Seeder
{
    public function run()
    {
        $reactions = [
            ['name' => 'like', 'icon' => 'fas fa-thumbs-up', 'color' => 'text-blue-600'],
            ['name' => 'love', 'icon' => 'fas fa-heart', 'color' => 'text-red-600'],
            ['name' => 'haha', 'icon' => 'fas fa-laugh', 'color' => 'text-yellow-600'],
            ['name' => 'wow', 'icon' => 'fas fa-surprise', 'color' => 'text-yellow-500'],
            ['name' => 'sad', 'icon' => 'fas fa-sad-tear', 'color' => 'text-blue-500'],
            ['name' => 'angry', 'icon' => 'fas fa-angry', 'color' => 'text-red-700'],
        ];

        foreach ($reactions as $reaction) {
            Reaction::create($reaction);
        }
    }
}
