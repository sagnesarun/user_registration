<?php
include '../models/Post.php';

class PostController {
    public function createPost($content, $user_id) {
        $postModel = new Post();
        $postModel->create($content, $user_id);
    }

    public function getAllPosts() {
        $postModel = new Post();
        return $postModel->getAll();
    }
}
