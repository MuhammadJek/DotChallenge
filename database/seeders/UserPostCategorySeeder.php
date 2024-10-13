<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserPostCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Category::factory()
            ->count(5)
            ->create();

        // Create 10 users, each with 3 posts
        User::factory()
            ->count(10)
            ->create();

        Post::factory()
            ->count(10)
            ->create();
    }
}
