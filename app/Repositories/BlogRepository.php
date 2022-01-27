<?php

namespace App\Repositories;

use App\Models\Post;
use App\Models\PostCategory;
use App\Interfaces\BlogInterface;

class BlogRepository implements BlogInterface {

    public function getPost($post_slug)
    {
        $post = Post::where('slug', $post_slug)->firstOrFail();
        return $post;
    }


    private function getCategory($name)
    {
        $category = PostCategory::where('name', $name)->first();
        return $category;
    }


    public function createCategory($name, $description = null)
    {
        return PostCategory::create([
            'name' => $name,
            'description' => $description
        ]);
    }



    public function createPost($request)
    {
        $category = $this->getCategory(
            $request->category != null 
            ? $request->category 
            : 'uncategorized'
        );

        if (!$category) {
            $newCategory = $this->createCategory($request->getCategory);
            $postCategory = $newCategory->id;
        }else{
            $postCategory = $category->id;
        }
        $image = uploadFile($request->file('image'), now()->format('Y') .'/posts');
        $user = auth()->user();

        $post = Post::create([
            'user_id' => $user->id,
            'category_id' => $category != null ? $postCategory : null,
            'title' => $request->title,
            'slug' => createUrlSlug($request->title),
            'featured_image' => $request->image != null ? $image : null,
            'body' => $request->body
        ]);

        return $post;
    }


    public function updatePost($request)
    {
        $post = $this->getPost($request->post);

        $user = auth()->user();
        if ($post->user->id != $user->id) {
            return false;
        }

        $category = $this->getCategory( $request->category );

        if (!$category) {
            $postCategory = $post->category_id;
        } else {
            $postCategory = $category->id;
        }

        $image = uploadFile($request->file('image'), now()->format('Y') . '/posts');
        

        $post->update([
            'category_id' => $postCategory,
            'title' => $request->title != null ? $request->title : $post->title,
            'featured_image' => $request->image != null ? $image : null,
            'body' => $request->body != null ? $request->body : $post->body
        ]);

        return $post;
    }


    public function deletePost($request)
    {
        $post = $this->getPost($request->post);

        $user = auth()->user();
        if ($post->user->id != $user->id) {
            return false;
        }

        return $post->delete();
    }

    
}