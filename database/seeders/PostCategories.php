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
            [ 'name' => 'uncategorized', 'description' => 'Posts with no category' ]
        ];
        foreach ($categories as $category){
            PostCategory::create($category);
        }
    }
}
