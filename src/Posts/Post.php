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

    /**
     * Get taxonomies for a single post
     */
    public function getTaxonomies()
    {
        $taxonomies = $this->postType->listTaxonomies([$this->postId]);

        return empty($taxonomies[$this->postId]) ? null : $taxonomies[$this->postId];
    }

    /**
     * Get featured images for a single post
     */
    public function getPostFeaturedImage()
    {
        $featuredImages = $this->postType->listFeatureImages([$this->postId]);

        return empty($featuredImages[$this->postId]) ? null : $featuredImages[$this->postId];
    }

    /**
     * Get meta for a single post
     */
    public function getMeta()
    {
        $meta = $this->postType->listMeta([$this->postId]);

        return empty($meta[$this->postId]) ? null : $meta[$this->postId];
    }

    public function get()
    {
        $postData = $this->postType->listPostData([$this->postId]);

        return empty($postData[$this->postId]) ? null : $postData[$this->postId];
    }
}