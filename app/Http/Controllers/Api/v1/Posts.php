<?php

namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use App\Http\Traits\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Blog\SavePost;
use App\Http\Requests\Blog\UpdatePost;
use App\Interfaces\BlogInterface;

class Posts extends Controller
{
    use ApiResponse;


    public function __construct(BlogInterface $interface)
    {
        $this->interface = $interface;
    }

    /**
     * Get  post
     * @param String $slug
     * @return \App\Helpers\ApiResponse
     */
    public function get($slug)
    {
        try {

            $post = $this->interface->getPost($slug);
            return $this->success($post, 'request accepted!');

        } catch (\Throwable $err) {

            return $this->error($err->getMessage(), 500);
        }
    }

    /**
     * create post
     * @param \App\Http\Requests\Blog\CreatePost $request
     * @return \App\Helpers\ApiResponse
     */
    public function create(SavePost $request)
    {
        try {
            $createPost = $this->interface->createPost($request);
            return $this->success($createPost, 'request accepted!');

        } catch (\Throwable $err) {
            return $this->error($err->getMessage(), 500);
        }
    }


    /**
     * update post
     * @param \App\Http\Requests\Blog\UpdatePost $request
     * @return \App\Helpers\ApiResponse
     */
    public function update(UpdatePost $request)
    {
        try {

            $updatePost = $this->interface->updatePost($request);
            if ($updatePost != false) {
                return $this->success($updatePost, 'request accepted!');
            }
            return $this->error('Unauthorized', 401);

        } catch (\Throwable $err) {

            return $this->error($err->getMessage(), 500);
        }
    }


    /**
     * delete post
     * @param \App\Http\Requests\Blog\DeletePost $request
     * @return \App\Helpers\ApiResponse
     */
    public function delete(Request $request)
    {
        try {
            $request->validate([
                'post' => 'required|uuid'
            ]);

            $deletePost = $this->interface->deletePost($request);
            if ($deletePost != false) {
                return $this->success($deletePost, 'request accepted!');
            }
            return $this->error('Unauthorized', 401);
            
        } catch (\Throwable $err) {

            return $this->error($err->getMessage(), 500);
        }
    }

}
