<?php

namespace App\Interfaces;

interface BlogInterface {

    public function getPost($post_slug);

    public function createCategory($name, $description = null);

    public function createPost($request);

    public function updatePost($request);

    public function deletePost($request);
    
}