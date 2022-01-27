<?php

namespace Database\Seeders;

use App\Models\PostCategory;
use Illuminate\Database\Seeder;

class PostCategories extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        PostCategory::truncate();
        $categories = [
            [ 'name' => 'uncategorized', 'decription' => 'Posts with no category' ]
        ];

        PostCategory::insert($categories);
    }
}
