<?php

namespace PeteKlein\Performant\Posts\Taxonomies;

class PostTaxonomies
{
    private $postId;
    public $terms = [];

    public function __construct(int $postId)
    {
        $this->postId = $postId;
    }

    public function add(\WP_Term $term) : void
    {
        $taxonomy = $term->taxonomy;
        if (empty($this->terms[$taxonomy])) {
            $this->terms[$taxonomy] = [];
        }
        $this->terms[$taxonomy][] = $term;
    }

    public function get(string $taxonomy) : array
    {
        if (!empty($this->terms[$taxonomy])) {
            return $this->terms[$taxonomy];
        }

        return null;
    }

    public function list()
    {
        return $this->terms;
    }
}
