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

    /**
     * Add a taxonomy to the taxonomies list
     *
     * @param \WP_Term $term
     */
    public function add(\WP_Term $term) : void
    {
        $taxonomy = $term->taxonomy;
        if (empty($this->terms[$taxonomy])) {
            $this->terms[$taxonomy] = [];
        }
        $this->terms[$taxonomy][] = $term;
    }

    /**
     * Get a taxonomy by taxonomy slug
     *
     * @param string $taxonomy
     */
    public function get(string $taxonomy) : array
    {
        if (!empty($this->terms[$taxonomy])) {
            return $this->terms[$taxonomy];
        }

        return null;
    }

    /**
     * Return the list of terms
     */
    public function list() : array
    {
        return $this->terms;
    }
}
