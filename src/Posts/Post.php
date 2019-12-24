<?php

namespace PeteKlein\Performant\Posts;

class Post {
    private $postId;
    private $postType;

    public function __construct(int $postId, PostTypeBase $postType)
    {
        $this->postId = $postId;
        $this->postType = $postType;
    }
}